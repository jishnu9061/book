<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Events\AssignedUser;
use App\Events\TicketCreated;
use App\Events\TicketNewComment;
use App\Events\TicketUpdated;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Priority;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\TicketEntry;
use App\Models\TicketField;
use App\Services\TicketWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TicketController extends BaseApiController
{
    /**
     * Display a listing of tickets.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user()->load('role');
        $byCustomer = null;
        $byAssign = null;

        if (in_array($user->role->slug, ['customer'])) {
            $byCustomer = $user->id;
        } elseif (in_array($user->role->slug, ['agent'])) {
            $byAssign = $user->id;
        } else {
            $byAssign = $request->input('assigned_to');
        }

        [$perPage, $page] = $this->getPaginationParams($request);

        $query = Ticket::query()
            ->byCustomer($byCustomer)
            ->byAssign($byAssign)
            ->with(['user', 'assignedTo', 'status', 'priority', 'category', 'department']);

        // Apply filters
        $filters = $request->only(['search', 'priority_id', 'status_id', 'type_id', 'category_id', 'department_id', 'user_id', 'assigned_to']);
        $query = $query->filter($filters);

        // Apply date filters
        if ($request->has('date_from') || $request->has('date_to')) {
            $from = $request->date_from ? \Carbon\Carbon::parse($request->date_from)->startOfDay() : null;
            $to = $request->date_to ? \Carbon\Carbon::parse($request->date_to)->endOfDay() : null;

            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            } elseif ($from) {
                $query->where('created_at', '>=', $from);
            } elseif ($to) {
                $query->where('created_at', '<=', $to);
            }
        }

        // Apply type filters
        if ($request->has('type')) {
            $type = $request->type;
            if ($type === 'un_assigned') {
                $query->whereNull('assigned_to');
            } elseif ($type === 'open') {
                $openedStatus = Status::where('slug', 'like', '%closed%')->first();
                if ($openedStatus) {
                    $query->where('status_id', '!=', $openedStatus->id);
                }
            } elseif ($type === 'new') {
                $query->where('created_at', '>=', date('Y-m-d') . ' 00:00:00');
            }
        }

        // Apply sorting
        $allowedSorts = ['id', 'subject', 'created_at', 'updated_at', 'priority_id', 'status_id'];
        $query = $this->applySorting($query, $request, $allowedSorts, 'id');

        // Apply includes
        $defaultIncludes = ['user', 'assignedTo', 'status', 'priority', 'category', 'department'];
        $query = $this->applyIncludes($query, $request, $defaultIncludes);

        $tickets = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($tickets, [
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created ticket.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $requiredFields = [];
        $getRequiredFields = Setting::where('slug', 'required_ticket_fields')->first();
        if (!empty($getRequiredFields)) {
            $requiredFields = json_decode($getRequiredFields->value, true);
        }

        $user = Auth::user();
        $requestData = $request->validate([
            'user_id' => ['nullable', Rule::exists('users', 'id')],
            'priority_id' => ['nullable', Rule::exists('priorities', 'id')],
            'status_id' => ['nullable', Rule::exists('status', 'id')],
            'department_id' => [in_array('department', $requiredFields) ? 'required' : 'nullable', Rule::exists('departments', 'id')],
            'assigned_to' => [in_array('assigned_to', $requiredFields) ? 'required' : 'nullable', Rule::exists('users', 'id')],
            'category_id' => [in_array('category', $requiredFields) ? 'required' : 'nullable', Rule::exists('categories', 'id')],
            'sub_category_id' => [in_array('sub_category', $requiredFields) ? 'required' : 'nullable', Rule::exists('categories', 'id')],
            'type_id' => [in_array('ticket_type', $requiredFields) ? 'required' : 'nullable', Rule::exists('types', 'id')],
            'subject' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'custom_field' => ['nullable', 'array'],
        ]);

        if (in_array($user->role->slug, ['customer'])) {
            $requestData['user_id'] = $user->id;
        }

        if (is_null($requestData['priority_id'] ?? null)) {
            $priority = Priority::orderBy('name')->first();
            if (!empty($priority)) {
                $requestData['priority_id'] = $priority->id;
            }
        }

        if (is_null($requestData['status_id'] ?? null)) {
            $status = Status::where('slug', 'like', '%active%')->first();
            if (!empty($status)) {
                $requestData['status_id'] = $status->id;
            }
        }

        $requestData['created_by'] = $user->id;
        $ticket = Ticket::create($requestData);

        // Handle file uploads
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $file) {
                $filePath = $file->store('tickets', ['disk' => 'file_uploads']);
                Attachment::create([
                    'ticket_id' => $ticket->id,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'path' => $filePath,
                ]);
            }
        }

        // Handle custom fields
        $customInputs = $request->input('custom_field', []);
        if (!empty($customInputs)) {
            foreach ($customInputs as $cfk => $cfv) {
                $ticketField = TicketField::where('name', $cfk)->first();
                if (!empty($ticketField)) {
                    TicketEntry::create([
                        'ticket_id' => $ticket->id,
                        'field_id' => $ticketField->id,
                        'name' => $cfk,
                        'label' => $ticketField->label,
                        'value' => $cfv,
                    ]);
                }
            }
        }

        // Process the new ticket through workflow
        TicketWorkflowService::processNewTicket($ticket);

        // Enhanced event data for new notification system
        event(new TicketCreated([
            'ticket_id' => $ticket->id,
            'created_by' => auth()->id(),
            'is_public' => false,
            'assigned_to' => $ticket->assigned_to,
        ]));

        if (!empty($ticket->assigned_to)) {
            event(new AssignedUser([
                'ticket_id' => $ticket->id,
                'assigned_to' => $ticket->assigned_to,
                'assigned_by' => auth()->id(),
            ]));
        }

        return $this->successResponse($ticket->load(['user', 'assignedTo', 'status', 'priority', 'category', 'department']), 201);
    }

    /**
     * Display the specified ticket.
     *
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Auth::user()->load('role');
        $byCustomer = null;
        $byAssign = null;

        if (in_array($user->role->slug, ['customer'])) {
            $byCustomer = $user->id;
        } elseif (in_array($user->role->slug, ['agent'])) {
            $byAssign = $user->id;
        }

        $ticket = Ticket::byCustomer($byCustomer)
            ->byAssign($byAssign)
            ->where(function ($query) use ($id) {
                $query->where('uid', $id)
                    ->orWhere('id', $id);
            })
            ->with(['user', 'assignedTo', 'status', 'priority', 'category', 'subCategory', 'department', 'ticketType', 'contact', 'attachments', 'comments.user', 'ticketEntries'])
            ->first();

        if (empty($ticket)) {
            return $this->notFoundResponse('Ticket not found');
        }

        return $this->successResponse($ticket);
    }

    /**
     * Update the specified ticket.
     *
     * @param Request $request
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user()->load('role');
        $byCustomer = null;
        $byAssign = null;

        if (in_array($user->role->slug, ['customer'])) {
            $byCustomer = $user->id;
        } elseif (in_array($user->role->slug, ['agent'])) {
            $byAssign = $user->id;
        }

        $ticket = Ticket::byCustomer($byCustomer)
            ->byAssign($byAssign)
            ->where(function ($query) use ($id) {
                $query->where('uid', $id)
                    ->orWhere('id', $id);
            })
            ->first();

        if (empty($ticket)) {
            return $this->notFoundResponse('Ticket not found');
        }

        // Check permissions
        if (in_array($user->role->slug, ['customer']) && $ticket->user_id !== $user->id) {
            return $this->forbiddenResponse('You can only update your own tickets');
        }

        $requestData = $request->validate([
            'subject' => ['sometimes', 'required', 'string', 'max:255'],
            'details' => ['sometimes', 'required', 'string'],
            'priority_id' => ['sometimes', 'nullable', Rule::exists('priorities', 'id')],
            'status_id' => ['sometimes', 'nullable', Rule::exists('status', 'id')],
            'department_id' => ['sometimes', 'nullable', Rule::exists('departments', 'id')],
            'assigned_to' => ['sometimes', 'nullable', Rule::exists('users', 'id')],
            'category_id' => ['sometimes', 'nullable', Rule::exists('categories', 'id')],
            'sub_category_id' => ['sometimes', 'nullable', Rule::exists('categories', 'id')],
            'type_id' => ['sometimes', 'nullable', Rule::exists('types', 'id')],
            'resolution' => ['sometimes', 'nullable', 'string'],
            'tags' => ['sometimes', 'nullable', 'array'],
        ]);

        $ticket->update($requestData);

        event(new TicketUpdated([
            'ticket_id' => $ticket->id,
            'updated_by' => auth()->id(),
        ]));

        if ($request->has('assigned_to') && $ticket->assigned_to) {
            event(new AssignedUser([
                'ticket_id' => $ticket->id,
                'assigned_to' => $ticket->assigned_to,
                'assigned_by' => auth()->id(),
            ]));
        }

        return $this->successResponse($ticket->load(['user', 'assignedTo', 'status', 'priority', 'category', 'department']));
    }

    /**
     * Remove the specified ticket.
     *
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user()->load('role');
        $byCustomer = null;
        $byAssign = null;

        if (in_array($user->role->slug, ['customer'])) {
            $byCustomer = $user->id;
        } elseif (in_array($user->role->slug, ['agent'])) {
            $byAssign = $user->id;
        }

        $ticket = Ticket::byCustomer($byCustomer)
            ->byAssign($byAssign)
            ->where(function ($query) use ($id) {
                $query->where('uid', $id)
                    ->orWhere('id', $id);
            })
            ->first();

        if (empty($ticket)) {
            return $this->notFoundResponse('Ticket not found');
        }

        $ticket->delete();

        return $this->successResponse(null, 204);
    }

    /**
     * Restore a soft-deleted ticket.
     *
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $ticket = Ticket::withTrashed()
            ->where(function ($query) use ($id) {
                $query->where('uid', $id)
                    ->orWhere('id', $id);
            })
            ->first();

        if (empty($ticket)) {
            return $this->notFoundResponse('Ticket not found');
        }

        $ticket->restore();

        return $this->successResponse($ticket->load(['user', 'assignedTo', 'status', 'priority', 'category', 'department']));
    }

    /**
     * Add a comment to a ticket.
     *
     * @param Request $request
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addComment(Request $request, $id)
    {
        $ticket = Ticket::where(function ($query) use ($id) {
            $query->where('uid', $id)
                ->orWhere('id', $id);
        })->first();

        if (empty($ticket)) {
            return $this->notFoundResponse('Ticket not found');
        }

        $requestData = $request->validate([
            'comment' => ['required', 'string'],
            'is_internal' => ['sometimes', 'boolean'],
        ]);

        $user = Auth::user();
        $comment = Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'comment' => $requestData['comment'],
            'is_internal' => $requestData['is_internal'] ?? false,
        ]);

        event(new TicketNewComment([
            'ticket_id' => $ticket->id,
            'comment_id' => $comment->id,
            'user_id' => $user->id,
        ]));

        return $this->successResponse($comment->load('user'), 201);
    }

    /**
     * Get comments for a ticket.
     *
     * @param Request $request
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComments(Request $request, $id)
    {
        $ticket = Ticket::where(function ($query) use ($id) {
            $query->where('uid', $id)
                ->orWhere('id', $id);
        })->first();

        if (empty($ticket)) {
            return $this->notFoundResponse('Ticket not found');
        }

        [$perPage, $page] = $this->getPaginationParams($request);

        $comments = Comment::where('ticket_id', $ticket->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($comments);
    }

    /**
     * Get conversations for a ticket.
     *
     * @param Request $request
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConversations(Request $request, $id)
    {
        $ticket = Ticket::where(function ($query) use ($id) {
            $query->where('uid', $id)
                ->orWhere('id', $id);
        })->first();

        if (empty($ticket)) {
            return $this->notFoundResponse('Ticket not found');
        }

        [$perPage, $page] = $this->getPaginationParams($request);

        $conversations = $ticket->conversations()
            ->with(['contact', 'messages.user', 'messages.contact'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($conversations);
    }

    /**
     * Import tickets from CSV.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        // Implementation would go here
        // This is a placeholder - actual CSV import logic would need to be implemented

        return $this->errorResponse('CSV import not yet implemented', 501);
    }

    /**
     * Export tickets to CSV.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $user = Auth::user()->load('role');
        $byCustomer = null;
        $byAssign = null;

        if (in_array($user->role->slug, ['customer'])) {
            $byCustomer = $user->id;
        } elseif (in_array($user->role->slug, ['agent'])) {
            $byAssign = $user->id;
        }

        $query = Ticket::byCustomer($byCustomer)
            ->byAssign($byAssign)
            ->with(['priority', 'category', 'subCategory', 'department', 'status', 'assignedTo']);

        // Apply filters if provided
        $filters = $request->only(['search', 'priority_id', 'status_id', 'type_id', 'category_id', 'department_id']);
        if (!empty($filters)) {
            $query = $query->filter($filters);
        }

        $filename = 'tickets_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ];

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'UID', 'Subject', 'Priority', 'Category', 'Sub Category', 'Department',
                'Status', 'Assigned To Email', 'Assigned To Name', 'Created',
            ]);

            $query->orderBy('id')
                ->chunkById(1000, function ($tickets) use ($handle) {
                    foreach ($tickets as $ticket) {
                        fputcsv($handle, [
                            $ticket->uid,
                            $ticket->subject,
                            optional($ticket->priority)->name,
                            optional($ticket->category)->name,
                            optional($ticket->subCategory)->name,
                            optional($ticket->department)->name,
                            optional($ticket->status)->name,
                            optional($ticket->assignedTo)->email,
                            $ticket->assignedTo ? ($ticket->assignedTo->first_name . ' ' . $ticket->assignedTo->last_name) : null,
                            $ticket->created_at,
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, $headers);
    }
}

