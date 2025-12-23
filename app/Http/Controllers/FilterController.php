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
        $customerRole = Role::where('slug', 'customer')->first();
        $customers = User::where('role_id', '!=', $customerRole ? $customerRole->id : 0)
            ->filter(Request::only('search'))
            ->limit(6)
            ->get()
            ->map
            ->only('id', 'name');
        return response()->json($customers);
    }
}
