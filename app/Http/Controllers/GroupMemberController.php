<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupMember;
use App\Models\Group;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use App\Models\Member;
use DataTables;

class GroupMemberController extends Controller
{

    public function __construct()
    {
        date_default_timezone_set(get_option('timezone', 'Africa/Accra'));
    }

    private function formatPayoutDate($date)
    {
        return $date ? date(get_option('date_format', 'Y-m-d'), strtotime($date)) : '';
    }


    public function index($group_id)
    {
        $group = Group::findOrFail($group_id);
        return view('backend.group_members.list', compact('group'));
    }

    public function get_table_data($group_id)
    {
        $members = GroupMember::where('group_id', $group_id)
            ->with('member')
            ->with('savingsAccount')
            ->select('group_members.*')
            //order by payout position number
            ->orderBy('payout_position_number', 'asc');

        return DataTables::eloquent($members)
            ->addColumn('member.first_name', function ($member) {
                return $member->member->first_name . ' ' . $member->member->last_name;
            })
            ->addColumn('savings_account.account_number', function ($member) {
                return $member->savingsAccount->account_number;
            })
            ->addColumn('payout_position_number', function ($member) {
                return $member->payout_position_number;
            })  
            ->editColumn('has_received_payout', function ($member) {
                return $member->has_received_payout ? 'Yes' : 'No';
            })
            ->editColumn('has_received_payout_date', function ($member) {
                return $this->formatPayoutDate($member->has_received_payout_date) ? $this->formatPayoutDate($member->has_received_payout_date) : 'N/A';
            })
            ->addColumn('amount_received', function ($member) {
                return $member->amount_received;
            })
            ->addColumn('action', function ($member) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '&nbsp;</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item ajax-modal" href="' . route('group_members.edit', $member['id']) . '" data-title="' . _lang('Switch Payout Position') . '"><i class="ti-exchange-vertical"></i> ' . _lang('Switch Member Position') . '</a>'
                 . '<a class="dropdown-item" href="#" onclick="updatePayoutStatus(' . $member->id . ')"><i class="ti-check"></i> ' . _lang('Toggle Payout Status') . '</a>'

                . '<form action="' . route('group_members.destroy', $member['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                . '</form>'
                . '</div>'
                . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getMembersByGroup($group_id)
    {
        // Fetch members from the group where has_received_payout is false
        $members = GroupMember::where('group_id', $group_id)
                    ->where('has_received_payout', false)
                    ->join('members', 'group_members.member_id', '=', 'members.id')
                    ->select('members.id', 'group_members.savings_account_id', 'group_members.payout_position_number', 'members.first_name', 'members.last_name', 'members.member_no')
                    ->get();

        // Return JSON response
        return response()->json(['members' => $members]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'member_id' => 'required|exists:members,id',
            'savings_account_id' => 'required|exists:savings_accounts,id',
            'opening_balance' => 'required|numeric|min:1',
            'collector_id' => 'required|exists:users,id', // Ensure collector is provided
        ]);

        // Check if group has reach it`s member limit
        $group = Group::find($request->group_id);
        if ($group->total_members >= $group->member_limit) {
            return response()->json(['result' => 'error','message' => 'Group has reached its member limit.']);
        }
    

        // Determine the next payout position (FCFS)
        $highestPayout = GroupMember::where('group_id', $request->group_id)->max('payout_position_number');
        $nextPayoutPosition = $highestPayout ? $highestPayout + 1 : 1;
    
        // Fetch the savings account
        $savingsAccount = SavingsAccount::find($request->savings_account_id);

        // Ensure the member does not already have a savings account in the group
        $existingAccount = GroupMember::where('group_id', $request->group_id)
            ->where('savings_account_id', $request->savings_account_id)
            ->first();
            if ($existingAccount) {
                return response()->json(['result' => 'error', 'message' => 'Account already exists! Please create a new Group Account for this member.']);
            }

    
        // Create new group member
        $groupMember = GroupMember::create([
            'group_id' => $request->group_id,
            'member_id' => $request->member_id,
            'savings_account_id' => $request->savings_account_id,
            'total_contributed' => $request->opening_balance,
            'payout_position_number' => $nextPayoutPosition,
        ]);

        // increase total_members and target_amount in Groups table (target_amount = monthly_contribution * total_members)
        $group = Group::find($request->group_id);
        $group->total_members += 1;
        $group->target_amount += $group->monthly_contribution;
        $group->save();
                
    
        // Save contribution in Transactions table
        $transaction = new Transaction();
        $transaction->trans_date = now();
        $transaction->savings_account_id = $savingsAccount->id;
        $transaction->member_id = $savingsAccount->member_id;
        $transaction->amount = $request->opening_balance;
        $transaction->dr_cr = 'cr'; // Credit transaction
        $transaction->type = 'Deposit';
        $transaction->method = 'Manual';
        $transaction->status = 2; // Adjust status if needed
        $transaction->note = 'Contribution for Group Savings';
        $transaction->description = _lang('Group Susu Contribution');
        $transaction->collector_id = $request->collector_id;
        $transaction->created_user_id = auth()->id();
        $transaction->branch_id = auth()->user()->branch_id;
    
        $transaction->save();
    
        if($groupMember->id > 0 && $transaction->id > 0){
           if(!request()->ajax()){
               return redirect()->route('group_members.index', ['group_id' => $request->group_id])->with('success', _lang('Member Added Successfully'));
              }else{
                return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Member Added Successfully') ]);

                }
            }

    }
    
    public function edit(Request $request, $id)
    {
        $member = GroupMember::find($id);
        return view('backend.group_members.modal.edit', compact('member', 'id'));
    }
   
    // public function payout(Request $request, $id)
    // {
    //     $member = GroupMember::find($id);
    //     return view('backend.group_members.modal.payout', compact('member', 'id'));
    // }

    public function create($group_id)
{
    $group = Group::find($group_id);

    if (!$group) {
        return response()->json(['error' => 'Group not found'], 404);
    }

    return view('backend.group_members.modal.create', compact('group'));
}

public function getPayoutPositionNumber($group_id)
{
    // Get the highest existing payout position number in the group
    $highestPayoutPosition = GroupMember::where('group_id', $group_id)
        ->max('payout_position_number');

    return response()->json([
        'highest_payout_position' => $highestPayoutPosition
    ]);
}

    public function updatePayoutStatus($id)
    {
        // Find the member
        $member = GroupMember::findOrFail($id);
    
        // Toggle the payout status
        $member->has_received_payout = !$member->has_received_payout;
        $member->save();
    
        // Determine new status text
        $newStatus = $member->has_received_payout ? 'Yes' : 'No';
    
        // Return JSON response with new status
        return response()->json([
            'result' => 'success',
            'message' => _lang('Status Updated Successfully'),
            'new_status' => $newStatus
        ]);
    }
    

    public function switchPayoutPosition(Request $request, $id)
    {
        $member = GroupMember::findOrFail($id);
        $request->validate([
            'new_position' => 'required|integer|min:1|max:100', // Adjust the min and max values as needed
        ]);

        $newPosition = $request->new_position;
        
        $existingMember = GroupMember::where('group_id', $member->group_id)
            ->where('payout_position_number', $newPosition)
            ->first();
        
        if ($existingMember) {
            $existingMember->payout_position_number = $member->payout_position_number;
            $existingMember->save();
        }

        $member->payout_position_number = $newPosition;
        $member->save();

        return response()->json(['result' => 'success', 'message' => _lang('Payout Position Switched Successfully')]);
    }

    public function resetContributionAndPayoutPosition($group_id)
    {
        // Fetch all group members
        $members = GroupMember::where('group_id', $group_id)->get();

        // Reset contribution and shuffle payout position for each member
        foreach ($members as $member) {
            $member->total_contributed = 0;
            $member->has_received_payout = false;
            $member->has_received_payout_date = null;
            $member->amount_received = 0;
            $member->save();
        }

        // Shuffle payout positions
        $shuffledPositions = $members->pluck('id')->shuffle();

        foreach ($members as $index => $member) {
            $member->payout_position_number = $index + 1;
            $member->save();
        }

        return response()->json(['result' => 'success', 'message' => _lang('Contribution and Payout Position Reset and Shuffled Successfully')]);
    }



    public function destroy($id)
    {
        $member = GroupMember::findOrFail($id);
        $group_id = $member->group_id; 
        $deleted_position = $member->payout_position_number;
    
        // Delete the member
        $member->delete();

    
        // Update payout positions for remaining members
        GroupMember::where('group_id', $group_id)
            ->where('payout_position_number', '>', $deleted_position)
            ->orderBy('payout_position_number')
            ->decrement('payout_position_number');

            //update total_members and target_amount in group table
            $group = Group::find($group_id);
            $group->total_members = GroupMember::where('group_id', $group_id)->count();
            $group->target_amount -= $group->monthly_contribution;
            $group->save();

        // Redirect to group members index page with success message
    
        return redirect()->route('group_members.index', ['group_id' => $group_id])
                         ->with('success', _lang('Member Removed Successfully'));
    }
    
}
