<?php

namespace App\Http\Controllers;

use App\Events\AssignedUser;
use App\Events\TicketCreated;
use App\Events\TicketNewComment;
use App\Events\TicketUpdated;
use App\Http\Middleware\RedirectIfNotParmitted;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Department;
use App\Models\PendingEmail;
use App\Models\Priority;
use App\Models\Review;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketEntry;
use App\Models\TicketField;
use App\Models\Type;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TicketsController extends Controller
{

    public function __construct(){
        $this->middleware(RedirectIfNotParmitted::class.':ticket');
    }

    public function index(){
        $byCustomer = null;
        $byAssign = null;
        $user = Auth()->user();
        $hiddenFields = Setting::where('slug', 'hide_ticket_fields')->first();
        if(in_array($user['role']['slug'], ['customer'])){
            $byCustomer = $user['id'];
        }elseif(in_array($user['role']['slug'], ['agent'])){
            $byAssign = $user['id'];
        }else{
            $byAssign = Request::input('assigned_to');
        }
        $whereAll = [];
        $type = Request::input('type');
        $limit = Request::input('limit', 10);
        $customer = Request::input('customer_id');

        if(!empty($customer)){
            $whereAll[] = ['user_id', '=', $customer];
        }

        if(!empty(Request::input('user_id'))){
            $whereAll[] = ['user_id', '=', Request::input('user_id')];
        }

        if($type == 'un_assigned'){
            $whereAll[] = ['assigned_to', '=', null];
        }elseif ($type == 'open'){
            $opened_status = Status::where('slug', 'like', '%closed%')->first();
            $whereAll[] = ['status_id', '!=', $opened_status->id];
        }elseif ($type == 'new'){
            $whereAll[] = ['created_at', '>=', date('Y-m-d').' 00:00:00'];
        }

        if (Request::input('date_from') || Request::input('date_to')) {
            $from = Request::input('date_from') ? Carbon::parse(Request::input('date_from'))->startOfDay() : null;
            $to = Request::input('date_to') ? Carbon::parse(Request::input('date_to'))->endOfDay() : null;

            if ($from && $to) {
                $whereAll[] = ['created_at', '>=', $from->toDateTimeString()];
                $whereAll[] = ['created_at', '<=', $to->toDateTimeString()];
            } elseif ($from) {
                $whereAll[] = ['created_at', '>=', $from->toDateTimeString()];
            } elseif ($to) {
                $whereAll[] = ['created_at', '<=', $to->toDateTimeString()];
            }
        }

        $ticketQuery = Ticket::where($whereAll);

        if (Request::has(['field', 'direction'])) {
            if(Request::input('field') == 'tech'){
                $ticketQuery
                    ->join('users', 'tickets.assigned_to', '=', 'users.id')
                    ->orderBy('users.first_name', Request::input('direction'))->select('tickets.*');
            }else{
                $ticketQuery->orderBy(Request::input('field'), Request::input('direction'));
            }
        }else{
            $ticketQuery->orderBy('updated_at', 'DESC');
        }

        return Inertia::render('Tickets/Index', [
            'title' => 'Tickets',
            'filters' => Request::all(),
            'hidden_fields' => $hiddenFields && $hiddenFields->value ? json_decode($hiddenFields->value) : null ,
            'priorities' => Priority::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'assignees' => [],
            'types' => Type::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'categories' => Category::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'departments' => Department::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'statuses' => Status::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'tickets' => $ticketQuery
                ->filter(Request::only(['search', 'priority_id', 'status_id', 'type_id', 'category_id', 'department_id']))
                ->byCustomer($byCustomer)
                ->byAssign($byAssign)
                ->paginate($limit)
                ->withQueryString()
                ->through(function ($ticket){
                    return [
                        'id' => $ticket->id,
                        'uid' => $ticket->uid,
                        'subject' => $ticket->subject,
                        'user' => $ticket->user ? $ticket->user->first_name.' '.$ticket->user->last_name : null,
                        'priority' => $ticket->priority ? $ticket->priority->name : null,
                        'category' => $ticket->category ? $ticket->category->name: null,
                        'sub_category' => $ticket->subCategory ? $ticket->subCategory->name: null,
                        'rating' => $ticket->review ? $ticket->review->rating : 0,
                        'status' => $ticket->status ? $ticket->status->name : null,
                        'due' => $ticket->due,
                        'assigned_to' => $ticket->assignedTo? $ticket->assignedTo->first_name.' '.$ticket->assignedTo->last_name : null,
                        'created_at' => $ticket->created_at,
                        'updated_at' => $ticket->updated_at,
                    ];
                }),
        ]);
    }

    public function csvImport()
    {
        $file = Request::file('file');
        if(!empty($file)){

            $fileContents = $this->csvToArray($file->getPathname());
            foreach ($fileContents as $data) {
                $findExistingTicket = Ticket::where('uid', $data['UID'])->first();
                if(empty($findExistingTicket)){
                    $priority = Priority::firstOrCreate(['name' => $data['Priority']]);
                    $category = Category::firstOrCreate(['name' => $data['Category']]);
                    $sub_category = Category::firstOrCreate(['name' => $data['Sub Category']]);
                    $department = Department::firstOrCreate(['name' => $data['Department']]);
                    $status = Status::firstOrCreate(['name' => $data['Status']]);
                    $assignTo = User::where(['email' => $data['Assigned To Email']])->first();
                    if(empty($assignTo) && !empty($data['Assigned To Email']) && !empty($data['Assigned To Name'])){
                        $aName = $this->splitName($data['Assigned To Name']);
                        $assignTo = User::create(['email' => $data['Assigned To Email'], 'first_name' => $aName[0], 'last_name' => $aName[1]]);
                    }

                    $ticket = Ticket::create([
                        'uid' => $data['UID'],
                        'subject' => $data['Subject'],
                        'priority_id' => $priority->id,
                        'category_id' => $category->id,
                        'sub_category_id' => $sub_category->id,
                        'department_id' => $department->id,
                        'status_id' => $status->id,
                        'assigned_to' => $assignTo?$assignTo->id:null
                    ]);
                }
            }
            return redirect()->back()->with('success', 'CSV file imported successfully.');
        }else{
            return redirect()->back()->with('error', 'CSV file import issue!');
        }
    }

    public function csvExport()
    {
        $filename = 'tickets.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ];

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // Optional: uncomment next line if you need a UTF-8 BOM for Excel compatibility
            // fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, [
                'UID', 'Subject', 'Priority', 'Category', 'Sub Category', 'Department',
                'Status', 'Assigned To Email', 'Assigned To Name', 'Created'
            ]);

            Ticket::with(['priority', 'category', 'subCategory', 'department', 'status', 'assignedTo'])
                ->orderBy('id')
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

    public function create(){
        $user = Auth()->user();
        $roles = Role::pluck('id', 'slug')->all();
        $hiddenFields = Setting::where('slug', 'hide_ticket_fields')->first();
        $custom_fields = TicketField::get();
        return Inertia::render('Tickets/Create', [
            'title' => 'Create a new ticket',
            'custom_fields' => $custom_fields,
            'hidden_fields' => $hiddenFields && $hiddenFields->value ? json_decode($hiddenFields->value) : null ,
            'customers' => User::where('role_id', $roles['customer'] ?? 0)->orWhere('id', Request::input('customer_id'))->orderBy('first_name')
                ->limit(6)
                ->get()
                ->map
                ->only('id', 'name'),
            'usersExceptCustomers' => User::where('role_id', '!=', $roles['customer'] ?? 0)->orWhere('id', Request::input('user_id'))->orderBy('first_name')
                ->limit(6)
                ->get()
                ->map
                ->only('id', 'name'),
            'priorities' => Priority::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'departments' => Department::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'all_categories' => Category::orderBy('name')
                ->get(),
            'statuses' => Status::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'types' => Type::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
        ]);
    }

    public function store(Request $request) {
        $required_fields = [];

        $get_required_fields = Setting::where('slug', 'required_ticket_fields')->first();
        if(!empty($get_required_fields)){
            $required_fields = json_decode($get_required_fields->value, true);
        }
        $user = Auth()->user();
        $request_data = Request::validate([
            'user_id' => ['nullable', Rule::exists('users', 'id')],
            'priority_id' => ['nullable', Rule::exists('priorities', 'id')],
            'status_id' => ['nullable', Rule::exists('status', 'id')],
            'department_id' => [in_array('department', $required_fields)?'required':'nullable', Rule::exists('departments', 'id')],
            'assigned_to' => [in_array('assigned_to', $required_fields)?'required':'nullable', Rule::exists('users', 'id')],
            'category_id' => [in_array('category', $required_fields)?'required':'nullable', Rule::exists('categories', 'id')],
            'sub_category_id' => [in_array('sub_category', $required_fields)?'required':'nullable', Rule::exists('categories', 'id')],
            'type_id' => [in_array('ticket_type', $required_fields)?'required':'nullable', Rule::exists('types', 'id')],
            'subject' => ['required'],
            'details' => ['required'],
        ]);

        if(in_array($user['role']['slug'], ['customer'])){
            $request_data['user_id'] = $user['id'];
        }

        if(is_null($request_data['priority_id'])){
            $priority = Priority::orderBy('name')->first();
            if(!empty($priority)){
                $request_data['priority_id'] = $priority->id;
            }
        }

        if(is_null($request_data['status_id'])){
            $status = Status::where('slug', 'like', '%active%')->first();
            if(!empty($status)){
                $request_data['status_id'] = $status->id;
            }
        }

        $request_data['created_by'] = $user['id'];
        $ticket = Ticket::create($request_data);

        if(Request::hasFile('files')){
            $files = Request::file('files');
            foreach($files as $file){
                $file_path = $file->store('tickets', ['disk' => 'file_uploads']);
                Attachment::create(['ticket_id' => $ticket->id, 'name' => $file->getClientOriginalName(), 'size' => $file->getSize(), 'path' => $file_path]);
            }
        }

        $custom_inputs = Request::input('custom_field');

        if(!empty($custom_inputs)){
            foreach ($custom_inputs as $cfk => $cfv){
                $ticket_field = TicketField::where('name', $cfk)->first();
                if(!empty($ticket_field)){
                    TicketEntry::create(['ticket_id' => $ticket->id, 'field_id' => $ticket_field->id, 'name' => $cfk, 'label' => $ticket_field->label, 'value' => $cfv]);
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
            'assigned_to' => $ticket->assigned_to
        ]));

        if(!empty($ticket->assigned_to)){
            event(new AssignedUser([
                'ticket_id' => $ticket->id,
                'assigned_to' => $ticket->assigned_to,
                'assigned_by' => auth()->id()
            ]));
        }

        return Redirect::route('tickets')->with('success', 'Ticket created.');
    }

    public function show($uid){

        $user = Auth()->user()->load('role');
        $byCustomer = null;
        $byAssign = null;
        if(in_array($user['role']['slug'], ['customer'])){
            $byCustomer = $user['id'];
        }elseif(in_array($user['role']['slug'], ['agent'])){
            $byAssign = $user['id'];
        }else{
            $byAssign = Request::input('assigned_to');
        }
        $ticket = Ticket::byCustomer($byCustomer)
            ->byAssign($byAssign)
            ->where(function($query) use ($uid){
                $query->where('uid', $uid);
                $query->orWhere('id', $uid);
            })->first();
        if(empty($ticket)){
            abort(404);
        }

        $roles = Role::pluck('id', 'slug')->all();

        $data = [
            'title' => $ticket->subject ? '#'.$ticket->uid.' '.$ticket->subject : '',
            'entries' => TicketEntry::where('ticket_id', $ticket->id)->get(),
            'attachments' => Attachment::orderBy('name')->with('user')->where('ticket_id', $ticket->id??null)->get(),
            'comments' => Comment::orderBy('created_at', 'asc')->with('user')->where('ticket_id', $ticket->id??null)->get(),
            'activities' => TicketActivity::where('ticket_id', $ticket->id)
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->get(),
            'conversations' => $this->getUserAccessibleConversations($ticket, $user),
            'ticket' => [
                'id' => $ticket->id,
                'uid' => $ticket->uid,
                'user_id' => $ticket->user_id,
                'contact_id' => $ticket->contact_id,
                'user' => $ticket->user?$ticket->user->name: 'N/A',
                'contact' => $ticket->contact?: null,
                'priority_id' => $ticket->priority_id,
                'created_at' => $ticket->created_at,
                'priority' => $ticket->priority? $ticket->priority->name : 'N/A',
                'status_id' => $ticket->status_id,
                'status' => $ticket->status?: null,
                'closed' => $ticket->status && $ticket->status->slug == 'closed',
                'review' => $ticket->review,
                'department_id' => $ticket->department_id,
                'department' => $ticket->department? $ticket->department->name : 'N/A',
                'category_id' => $ticket->category_id,
                'sub_category_id' => $ticket->sub_category_id,
                'category' => $ticket->category ? $ticket->category->name : 'N/A',
                'sub_category' => $ticket->subCategory ? $ticket->subCategory->name : 'N/A',
                'assigned_to' => $ticket->assigned_to,
                'assigned_user' => $ticket->assignedTo ? $ticket->assignedTo->first_name .' '.$ticket->assignedTo->last_name : 'N/A',
                'type_id' => $ticket->type_id,
                'type' => $ticket->ticketType ? $ticket->ticketType->name : 'N/A',
                'ticket_id' => $ticket->ticket_id,
                'subject' => $ticket->subject,
                'details' => $ticket->details,
                'resolution' => $ticket->resolution,
                'due_date' => $ticket->due_date,
                'estimated_hours' => $ticket->estimated_hours,
                'actual_hours' => $ticket->actual_hours,
                'source' => $ticket->source,
                'impact_level' => $ticket->impact_level,
                'urgency_level' => $ticket->urgency_level,
                'tags' => $ticket->tags,
                'custom_fields' => $ticket->custom_fields,
                'external_id' => $ticket->external_id,
                'last_customer_response' => $ticket->last_customer_response,
                'last_agent_response' => $ticket->last_agent_response,
                'sla_status' => $ticket->getSlaStatus(),
                'is_overdue' => $ticket->isOverdue(),
                'is_sla_breached' => $ticket->isSlaBreached(),
                'files' => [],
            ],
        ];

        return Inertia::render('Tickets/Show', [
            'title' => $ticket->subject ? '#'.$ticket->uid.' '.$ticket->subject : '',
            'entries' => TicketEntry::where('ticket_id', $ticket->id)->get(),
            'attachments' => Attachment::orderBy('name')->with('user')->where('ticket_id', $ticket->id??null)->get(),
            'comments' => Comment::orderBy('created_at', 'asc')->with('user')->where('ticket_id', $ticket->id??null)->get(),
            'activities' => TicketActivity::where('ticket_id', $ticket->id)
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->get(),
            'conversations' => $this->getUserAccessibleConversations($ticket, $user),
            'availableUsers' => User::whereHas('role', function($query) {
                $query->whereIn('slug', ['admin', 'manager', 'agent']);
            })->select('id', 'first_name', 'last_name', 'email')->get()->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email
                ];
            }),
            'user' => $user ? [
                'id' => $user->id,
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
                'email' => $user->email ?? '',
                'role' => $user->role ? [
                    'id' => $user->role->id,
                    'slug' => $user->role->slug ?? 'na',
                    'name' => $user->role->name ?? 'Not Assigned',
                    'access' => $user->role->access ?? null,
                ] : ['slug' => 'na', 'name' => 'Not Assigned'],
            ] : null,
            'ticket' => [
                'id' => $ticket->id,
                'uid' => $ticket->uid,
                'user_id' => $ticket->user_id,
                'contact_id' => $ticket->contact_id,
                'user' => $ticket->user?$ticket->user->name: 'N/A',
                'contact' => $ticket->contact?: null,
                'priority_id' => $ticket->priority_id,
                'created_at' => $ticket->created_at,
                'priority' => $ticket->priority? $ticket->priority->name : 'N/A',
                'status_id' => $ticket->status_id,
                'status' => $ticket->status?: null,
                'closed' => $ticket->status && $ticket->status->slug == 'closed',
                'review' => $ticket->review,
                'department_id' => $ticket->department_id,
                'department' => $ticket->department? $ticket->department->name : 'N/A',
                'category_id' => $ticket->category_id,
                'sub_category_id' => $ticket->sub_category_id,
                'category' => $ticket->category ? $ticket->category->name : 'N/A',
                'sub_category' => $ticket->subCategory ? $ticket->subCategory->name : 'N/A',
                'assigned_to' => $ticket->assigned_to,
                'assigned_user' => $ticket->assignedTo ? $ticket->assignedTo->first_name .' '.$ticket->assignedTo->last_name : 'N/A',
                'type_id' => $ticket->type_id,
                'type' => $ticket->ticketType ? $ticket->ticketType->name : 'N/A',
                'ticket_id' => $ticket->ticket_id,
                'subject' => $ticket->subject,
                'details' => $ticket->details,
                'resolution' => $ticket->resolution,
                'due_date' => $ticket->due_date,
                'estimated_hours' => $ticket->estimated_hours,
                'actual_hours' => $ticket->actual_hours,
                'source' => $ticket->source,
                'impact_level' => $ticket->impact_level,
                'urgency_level' => $ticket->urgency_level,
                'tags' => $ticket->tags,
                'custom_fields' => $ticket->custom_fields,
                'external_id' => $ticket->external_id,
                'last_customer_response' => $ticket->last_customer_response,
                'last_agent_response' => $ticket->last_agent_response,
                'sla_status' => $ticket->getSlaStatus(),
                'is_overdue' => $ticket->isOverdue(),
                'is_sla_breached' => $ticket->isSlaBreached(),
                'files' => [],
            ],
        ]);
    }

    public function edit($uid){
        $user = Auth()->user()->load('role');
        
        // Check if user has permission to update tickets
        if (!$user->access['ticket']['update']) {
            // Log unauthorized edit access attempt
            \Log::warning('Unauthorized ticket edit access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role->slug ?? 'unknown',
                'ticket_uid' => $uid,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            abort(403, 'You do not have permission to edit tickets');
        }
        
        $byCustomer = null;
        $byAssign = null;
        if(in_array($user['role']['slug'], ['customer'])){
            $byCustomer = $user['id'];
        }elseif(in_array($user['role']['slug'], ['agent'])){
            $byAssign = $user['id'];
        }else{
            $byAssign = Request::input('assigned_to');
        }
        $ticket = Ticket::byCustomer($byCustomer)
            ->byAssign($byAssign)
            ->where(function($query) use ($uid){
                $query->where('uid', $uid);
                $query->orWhere('id', $uid);
            })->first();
        if(empty($ticket)){
            abort(404);
        }
        
        // Additional check: Customers can only edit their own tickets
        if ($user->role->slug === 'customer' && $ticket->user_id !== $user->id) {
            \Log::warning('Customer attempted to edit another user\'s ticket', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'ticket_uid' => $ticket->uid,
                'ticket_owner_id' => $ticket->user_id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            abort(403, 'You can only edit your own tickets');
        }
        $hiddenFields = Setting::where('slug', 'hide_ticket_fields')->first();
        $comment_access = 'read';
        if($user['role']['slug'] === 'admin'){
            $comment_access = 'delete';
        }elseif($user['role']['slug'] === 'manager'){
//            $comment_access = 'view';
            $comment_access = 'delete';
        }elseif($user['role']['slug'] === 'agent'){
            $comment_access = 'delete';
        }

        $roles = Role::pluck('id', 'slug')->all();

        return Inertia::render('Tickets/Edit', [
            'hidden_fields' => $hiddenFields ? json_decode($hiddenFields->value) : null ,
            'title' => $ticket->subject ? '#'.$ticket->uid.' '.$ticket->subject : '',
            'entries' => TicketEntry::where('ticket_id', $ticket->id)->get(),
            'customers' => User::where('role_id', $roles['customer'] ?? 0)->orWhere('id', Request::input('customer_id'))->orderBy('first_name')
                ->limit(6)
                ->get()
                ->map
                ->only('id', 'name'),
            'usersExceptCustomers' => User::where('role_id', '!=', $roles['customer'] ?? 0)->orWhere('id', Request::input('user_id'))->orderBy('first_name')
                ->limit(6)
                ->get()
                ->map
                ->only('id', 'name'),
            'priorities' => Priority::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'departments' => Department::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'all_categories' => Category::orderBy('name')
                ->get(),
            'statuses' => Status::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'attachments' => Attachment::orderBy('name')->with('user')->where('ticket_id', $ticket->id??null)->get(),
            'comments' => Comment::orderBy('created_at', 'asc')->with('user')->where('ticket_id', $ticket->id??null)->get(),
            'types' => Type::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'ticket' => [
                'id' => $ticket->id,
                'uid' => $ticket->uid,
                'user_id' => $ticket->user_id,
                'contact_id' => $ticket->contact_id,
                'user' => $ticket->user?$ticket->user->name: 'N/A',
                'contact' => $ticket->contact?: null,
                'priority_id' => $ticket->priority_id,
                'created_at' => $ticket->created_at,
                'priority' => $ticket->priority? $ticket->priority->name : 'N/A',
                'status_id' => $ticket->status_id,
                'status' => $ticket->status?: null,
                'closed' => $ticket->status && $ticket->status->slug == 'closed',
                'review' => $ticket->review,
                'department_id' => $ticket->department_id,
                'department' => $ticket->department? $ticket->department->name : 'N/A',
                'category_id' => $ticket->category_id,
                'sub_category_id' => $ticket->sub_category_id,
                'category' => $ticket->category ? $ticket->category->name : 'N/A',
                'sub_category' => $ticket->subCategory ? $ticket->subCategory->name : 'N/A',
                'assigned_to' => $ticket->assigned_to,
                'assigned_user' => $ticket->assignedTo ? $ticket->assignedTo->first_name .' '.$ticket->assignedTo->last_name : 'N/A',
                'type_id' => $ticket->type_id,
                'type' => $ticket->ticketType ? $ticket->ticketType->name : 'N/A',
                'ticket_id' => $ticket->ticket_id,
                'subject' => $ticket->subject,
                'details' => $ticket->details,
                'resolution' => $ticket->resolution,
                'due_date' => $ticket->due_date,
                'estimated_hours' => $ticket->estimated_hours,
                'actual_hours' => $ticket->actual_hours,
                'source' => $ticket->source,
                'impact_level' => $ticket->impact_level,
                'urgency_level' => $ticket->urgency_level,
                'tags' => $ticket->tags,
                'custom_fields' => $ticket->custom_fields,
                'external_id' => $ticket->external_id,
                'last_customer_response' => $ticket->last_customer_response,
                'last_agent_response' => $ticket->last_agent_response,
                'sla_status' => $ticket->getSlaStatus(),
                'is_overdue' => $ticket->isOverdue(),
                'is_sla_breached' => $ticket->isSlaBreached(),
                'files' => [],
                'comment_access' => $comment_access,
            ],
        ]);
    }

    public function update(Ticket $ticket){
        $user = Auth()->user()->load('role');
        
        // Check if user has permission to update tickets
        if (!$user->access['ticket']['update']) {
            // Log unauthorized update attempt
            \Log::warning('Unauthorized ticket update attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role->slug ?? 'unknown',
                'ticket_id' => $ticket->id,
                'ticket_uid' => $ticket->uid,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            abort(403, 'You do not have permission to update tickets');
        }
        
        // Additional check: Customers can only update their own tickets
        if ($user->role->slug === 'customer' && $ticket->user_id !== $user->id) {
            \Log::warning('Customer attempted to update another user\'s ticket', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'ticket_uid' => $ticket->uid,
                'ticket_owner_id' => $ticket->user_id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            abort(403, 'You can only update your own tickets');
        }
        
        $request_data = Request::validate([
            'user_id' => ['nullable', Rule::exists('users', 'id')],
            'contact_id' => ['nullable', Rule::exists('contacts', 'id')],
            'priority_id' => ['nullable', Rule::exists('priorities', 'id')],
            'status_id' => ['nullable', Rule::exists('status', 'id')],
            'department_id' => ['nullable', Rule::exists('departments', 'id')],
            'assigned_to' => ['nullable', Rule::exists('users', 'id')],
            'category_id' => ['nullable', Rule::exists('categories', 'id')],
            'sub_category_id' => ['nullable', Rule::exists('categories', 'id')],
            'type_id' => ['nullable', Rule::exists('types', 'id')],
            'subject' => ['required'],
            'due' => ['nullable'],
            'details' => ['required'],
            'resolution' => ['nullable'],
            'due_date' => ['nullable', 'date'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0'],
            'actual_hours' => ['nullable', 'numeric', 'min:0'],
            'impact_level' => ['nullable', 'in:low,medium,high,critical'],
            'urgency_level' => ['nullable', 'in:low,medium,high,critical'],
            'source' => ['nullable', 'in:web,email,phone,chat,api'],
            'tags' => ['nullable', 'array'],
            'custom_fields' => ['nullable', 'array'],
            'external_id' => ['nullable', 'string', 'max:100'],
        ]);

        if(!empty(Request::input('review')) || !empty(Request::input('rating'))){
            $review = Review::create([
                'review' => Request::input('review'),
                'rating' => Request::input('rating'),
                'ticket_id' => $ticket->id,
                'user_id' => $user['id']
            ]);
            $ticket->update(['review_id' => $review->id]);
            return Redirect::route('tickets.edit', $ticket->uid)->with('success', 'Added the review!');
        }

        $closed_status = Status::where('slug', 'like', '%close%')->first();

        $update_message = null;
        if($closed_status && ($ticket->status_id != $closed_status->id) && $request_data['status_id'] == $closed_status->id){
            $update_message = 'The ticket has been closed.';
        }elseif($ticket->status_id != $request_data['status_id']){
            $update_message = 'The status has been changed for this ticket.';
        }

        if($ticket->priority_id != $request_data['priority_id']){
            $update_message = 'The priority has been changed for this ticket.';
        }

        if(empty($ticket->response) && in_array($user['role']['slug'], ['admin', 'manager', 'agent'])){
            $request_data['response'] = date('Y-m-d H:i:s');
        }

        if(isset($request_data['due']) && !empty($request_data['due'])){
            $request_data['due'] = date('Y-m-d', strtotime($request_data['due']));
        }

        // Handle due_date field properly
        if(isset($request_data['due_date'])){
            if(empty($request_data['due_date']) || $request_data['due_date'] === null){
                $request_data['due_date'] = null;
            } else {
                // Convert datetime-local format to proper datetime format
                $request_data['due_date'] = date('Y-m-d H:i:s', strtotime($request_data['due_date']));
            }
        }

        $assigned = (!empty($request_data['assigned_to']) && ($ticket->assigned_to != $request_data['assigned_to']))??false;

        // Track changes before updating
        $changes = [];
        foreach ($request_data as $field => $newValue) {
            $oldValue = $ticket->{$field};
            
            // Special handling for due_date to prevent unnecessary updates
            if ($field === 'due_date') {
                // Convert both values to comparable format
                $oldFormatted = $oldValue ? date('Y-m-d H:i:s', strtotime($oldValue)) : null;
                $newFormatted = $newValue ? date('Y-m-d H:i:s', strtotime($newValue)) : null;
                
                if ($oldFormatted !== $newFormatted) {
                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            } else {
                if ($oldValue != $newValue) {
                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            }
        }

        $ticket->update($request_data);

        // Process changes through workflow
        if (!empty($changes)) {
            TicketWorkflowService::processTicketUpdate($ticket, $changes, $user['id']);
        }

        if($assigned){
            event(new AssignedUser([
                'ticket_id' => $ticket->id,
                'assigned_to' => $ticket->assigned_to,
                'assigned_by' => auth()->id()
            ]));
        }

        if(!empty($update_message)){
            event(new TicketUpdated([
                'ticket_id' => $ticket->id,
                'changes' => $changes,
                'updated_by' => auth()->id(),
                'update_message' => $update_message
            ]));
        }

        if(!empty(Request::input('comment'))){
            Comment::create([
                'details' => Request::input('comment'),
                'ticket_id' => $ticket->id,
                'user_id' => $user['id']
            ]);
            $this->sendMailCron( $ticket->id, 'response' , Request::input('comment') );
        }

        $removedFiles = Request::input('removedFiles');
        if(!empty($removedFiles)){
            $attachments = Attachment::where('ticket_id', $ticket->id)->whereIn('id', $removedFiles)->get();
            foreach ($attachments as $attachment){
                if(Storage::disk('file_uploads')->exists($attachment->path)){
                    Storage::disk('file_uploads')->delete($attachment->path);
                }
                $attachment->delete();
            }
        }

        if(Request::hasFile('files')){
            $files = Request::file('files');
            foreach($files as $file){
                $file_path = $file->store('tickets', ['disk' => 'file_uploads']);
                Attachment::create(['ticket_id' => $ticket->id, 'user_id' => $user['id'], 'name' => $file->getClientOriginalName(), 'size' => $file->getSize(), 'path' => $file_path]);
            }
        }

        return Redirect::route('tickets.edit', $ticket->uid)->with('success', 'Ticket updated.');
    }

    public function newComment(){
        $request = Request::all();
        $ticket = Comment::where('ticket_id', $request['ticket_id'])->count();
        if(empty($ticket)){
            event(new TicketNewComment([
                'ticket_id' => $request['ticket_id'],
                'user_id' => auth()->id(),
                'comment' => $request['comment']
            ]));
        }

        $newComment = new Comment;
        if(isset($request['user_id'])){
            $newComment->user_id = $request['user_id'];
        }
        if(isset($request['ticket_id'])){
            $newComment->ticket_id = $request['ticket_id'];
        }
        $newComment->details = $request['comment'];

        $newComment->save();

        return response()->json($newComment);
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return Redirect::route('tickets')->with('success', 'Ticket deleted.');
    }

    public function restore(Ticket $ticket){
        $ticket->restore();
        return Redirect::back()->with('success', 'Ticket restored.');
    }

    private function sendMailCron($id, $type = null, $value = null){
        PendingEmail::create(['ticket_id' => $id, 'type' => $type, 'value' => $value]);
    }

    /**
     * Get conversations that the current user can access
     */
    private function getUserAccessibleConversations($ticket, $user)
    {
        $conversations = $ticket->conversations()
            ->with(['participants.user', 'messages.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Filter conversations based on user access
        $filteredConversations = $conversations->filter(function ($conversation) use ($user) {
            // Check if user is a participant in this conversation
            $isParticipant = $conversation->participants()
                ->where('user_id', $user->id)
                ->exists();
            
            // Check if user is admin/manager/agent
            $isAdmin = in_array($user->role->slug ?? '', ['admin', 'manager', 'agent']);
            
            // Determine access permissions
            // - Participants can always access conversations they're part of
            // - Admins can access internal conversations (but not customer conversations they're not part of)
            return $isParticipant || ($isAdmin && $conversation->type === 'internal');
        })->values(); // Reset array keys

        // Convert to arrays to avoid serialization issues
        return $filteredConversations->map(function ($conversation) {
            return [
                'id' => $conversation->id,
                'type' => $conversation->type,
                'title' => $conversation->title,
                'created_at' => $conversation->created_at,
                'updated_at' => $conversation->updated_at,
                'participants' => $conversation->participants->map(function ($participant) {
                    return [
                        'id' => $participant->id,
                        'user_id' => $participant->user_id,
                        'role' => $participant->role,
                        'user' => $participant->user ? [
                            'id' => $participant->user->id,
                            'first_name' => $participant->user->first_name ?? '',
                            'last_name' => $participant->user->last_name ?? '',
                            'email' => $participant->user->email ?? '',
                        ] : null,
                    ];
                })->values(),
                'messages' => $conversation->messages->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'created_at' => $message->created_at,
                        'updated_at' => $message->updated_at,
                        'user' => $message->user ? [
                            'id' => $message->user->id,
                            'first_name' => $message->user->first_name ?? '',
                            'last_name' => $message->user->last_name ?? '',
                            'email' => $message->user->email ?? '',
                        ] : null,
                    ];
                })->values(),
            ];
        })->values();
    }

    private function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    private function splitName($name) {
        $name = trim($name);
        $last_name = (!str_contains($name, ' ')) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim( preg_replace('#'.preg_quote($last_name,'#').'#', '', $name ) );
        return array($first_name, $last_name);
    }
}
