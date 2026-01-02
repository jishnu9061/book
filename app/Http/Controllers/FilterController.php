<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class FilterController extends Controller {
    //
    public function customers(){
        $customerRole = Role::where('slug', 'customer')->first();
        $customers = User::where('role_id', $customerRole ? $customerRole->id: 0)
            ->filter(Request::only('search'))
            ->limit(6)
            ->get()
            ->map
            ->only('id', 'name');

        return response()->json($customers);
    }

    public function assignees(){

        $search = Request::input('search');
        $assignees = [];
        if(!empty($search)){
            $ticketAssignees = Ticket::whereHas('assignedTo', function($q) use ($search){
                $q->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%');
            })->with('assignedTo:id,first_name,last_name')->select('assigned_to')->groupBy('assigned_to')->limit(5)->get();
        }else{
            $ticketAssignees = Ticket::whereHas('assignedTo')->with('assignedTo:id,first_name,last_name')->select('assigned_to')->groupBy('assigned_to')->limit(5)->get();
        }

        foreach ($ticketAssignees as $ticketAssignee){
            $assignees[] = ['id' => $ticketAssignee->assignedTo['id'], 'name' => $ticketAssignee->assignedTo['first_name'].' '.$ticketAssignee->assignedTo['last_name']];
        }

        return response()->json($assignees);
    }

    public function clients(){

        $search = Request::input('search');
        $all_clients = [];
        if(!empty($search)){
            $clients = Ticket::whereHas('user', function($q) use ($search){
                $q->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%');
            })->with('user:id,first_name,last_name')->select('user_id')->groupBy('user_id')->limit(10)->get();
        }else{
            $clients = Ticket::whereHas('user')->with('user:id,first_name,last_name')->select('user_id')->groupBy('user_id')->limit(10)->get();
        }

        foreach ($clients as $client){
            $all_clients[] = ['id' => $client->user['id'], 'name' => $client->user['first_name'].' '.$client->user['last_name']];
        }

        return response()->json($all_clients);
    }

    public function usersExceptCustomer(){
        $currentUser = auth()->user();
        if (!$currentUser) return response()->json([]);

        $roles = Role::pluck('id', 'slug')->all();
        $deptId = Request::input('department_id');
        
        $adminId = $roles['admin'] ?? 0;
        $managerId = $roles['manager'] ?? 0;
        $agentId = $roles['agent'] ?? 0;
        $generalId = $roles['general'] ?? 0;
        $customerId = $roles['customer'] ?? 0;

        $query = User::where('role_id', '!=', $customerId)
            ->filter(Request::only('search'));
        if ($currentUser->role_id == $adminId) {
            // Admins see Managers in the selected department only. No Admins allowed.
            if ($deptId) {
                $query->where('department_id', $deptId)
                      ->where('role_id', $managerId);
            } else {
                $query->where('id', 0);
            }
        } elseif ($currentUser->role_id == $managerId) {
            // Managers see Staff (General/Agent) in the selected department
            if ($deptId) {
                $query->where('department_id', $deptId)
                      ->whereIn('role_id', [$generalId, $agentId]);
            } else {
                $query->where('id', 0);
            }
        } else {
            // Fallback for Agents or other staff - only show department staff
            if ($deptId) {
                $query->where('department_id', $deptId);
            } else {
                $query->where('id', 0);
            }
        }

        $users = $query
            ->orderByRaw("CASE WHEN role_id = ? THEN 0 WHEN role_id = ? THEN 1 ELSE 2 END", [$managerId, $adminId])
            ->orderBy('first_name')
            ->limit(10)
            ->get()
            ->map
            ->only('id', 'name');

        return response()->json($users);
    }
}
