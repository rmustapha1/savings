<?php

namespace App\Http\Controllers;

use App\Models\SavingsAccount;
use App\Models\SavingsProduct;
use App\Models\Transaction;
use App\Models\Group;
use App\Models\GroupMember;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;

class SavingsAccountController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        date_default_timezone_set(get_option('timezone', 'Africa/Accra'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.savings_accounts.list');
    }

    public function get_table_data()
    {

        $savingsaccounts = SavingsAccount::with(['member', 'savings_type', 'savings_type.currency'])
            ->select('savings_accounts.*')
            ->withoutGlobalScopes(['status'])
            ->orderBy("savings_accounts.id", "desc");

        return Datatables::eloquent($savingsaccounts)
            ->editColumn('member.first_name', function ($savingsaccount) {
                return $savingsaccount->member->first_name . ' ' . $savingsaccount->member->last_name;
            })
            ->editColumn('status', function ($savingsaccount) {
                return status($savingsaccount->status);
            })
            ->editColumn('savings_type.name', function ($savingsaccount) {
                return $savingsaccount->savings_type->name . ' - ' . $savingsaccount->savings_type->currency->name;
            })
            ->filterColumn('member.first_name', function ($query, $keyword) {
                $query->whereHas('member', function ($query) use ($keyword) {
                    return $query->where("first_name", "like", "{$keyword}%")
                        ->orWhere("last_name", "like", "{$keyword}%");
                });
            }, true)
            ->addColumn('action', function ($savingsaccount) {
                return '<div class="dropdown text-center">'
                    . '<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '&nbsp;</button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item ajax-modal" href="' . route('savings_accounts.edit', $savingsaccount['id']) . '" data-title="' . _lang('Account Details') . '"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                    . '<a class="dropdown-item ajax-modal" href="' . route('savings_accounts.show', $savingsaccount['id']) . '" data-title="' . _lang('Update Account') . '"><i class="ti-eye"></i>  ' . _lang('View') . '</a>'
                    . '<form action="' . route('savings_accounts.destroy', $savingsaccount['id']) . '" method="post">'
                    . csrf_field()
                    . '<input name="_method" type="hidden" value="DELETE">'
                    . '<button class="dropdown-item btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($savingsaccount) {
                return "row_" . $savingsaccount->id;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.savings_accounts.modal.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_number'     => 'required|unique:savings_accounts|max:50',
            'member_id'          => 'required',
            'savings_product_id' => 'required',
            'status'             => 'required',
            'opening_balance'    => 'required|numeric',
        ]);
    
        // If Group Savings is selected, validate group_id; otherwise, validate member_id
        $accountType = SavingsProduct::find($request->savings_product_id);
        if ($accountType->id == 2) {
            $validator->addRules(['group_id' => 'required|exists:groups,id']);
            $validator->addRules(['payout_position_number' => 'required|numeric']);

            // Check if group has reached its member limit
            $group = Group::find($request->group_id);
            if ($group->total_members >= $group->member_limit) {
                if ($request->ajax()) {
                    return response()->json(['result' => 'error', 'message' => _lang('Group has reached its member limit')]);
                } else {
                    return back()->with('error', _lang('Group has reached its member limit'));
                }
            }
        }
    
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('savings_accounts.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }
    
        if ($request->opening_balance < $accountType->minimum_deposit_amount) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => _lang('You must deposit a minimum of') . ' ' . $accountType->minimum_deposit_amount . ' ' . $accountType->currency->name]);
            } else {
                return back()
                    ->with('error', _lang('You must deposit a minimum of') . ' ' . $accountType->minimum_deposit_amount . ' ' . $accountType->currency->name)
                    ->withInput();
            }
        }
    
        DB::beginTransaction();
    
        $savingsaccount                     = new SavingsAccount();
        $savingsaccount->account_number     = $accountType->account_number_prefix . $accountType->starting_account_number;
        $savingsaccount->member_id = $request->input('member_id');
        $savingsaccount->savings_product_id = $request->input('savings_product_id');
        $savingsaccount->status             = $request->input('status');
        $savingsaccount->opening_balance    = $request->input('opening_balance');
        $savingsaccount->description        = $request->input('description');
        $savingsaccount->created_user_id    = auth()->id();
        $savingsaccount->save();
    
        // Increment account number
        $accountType->starting_account_number += 1;
        $accountType->save();
    
        // Create Transaction
        $transaction                     = new Transaction();
        $transaction->trans_date         = now();
        $transaction->savings_account_id = $savingsaccount->id;
        $transaction->member_id          = $savingsaccount->member_id;
        $transaction->amount             = $request->input('opening_balance');
        $transaction->dr_cr              = 'cr';
        $transaction->type               = 'Deposit';
        $transaction->method             = 'Manual';
        $transaction->status             = 2;
        $transaction->note               = $request->input('note');
        $transaction->description        = _lang('Initial Deposit');
        $transaction->collector_id       = $request->input('collector_id');
        $transaction->created_user_id    = auth()->id();
        $transaction->branch_id          = auth()->user()->branch_id;
    
        $transaction->save();
       // Determine the next payout position (FCFS)
       $highestPayout = GroupMember::where('group_id', $request->group_id)->max('payout_position_number');
       $nextPayoutPosition = $highestPayout ? $highestPayout + 1 : 1;
   
        // If Group Savings, save member into Group Members table
        if ($accountType->id == 2) {
            DB::table('group_members')->insert([
                'group_id'                 => $request->input('group_id'),
                'member_id'                => $request->input('member_id'),
                'savings_account_id'       => $savingsaccount->id,
                'total_contributed'        => $request->input('opening_balance'),
                'payout_position_number'   => $nextPayoutPosition,
                'has_received_payout'      => false,
                'created_at'               => now(),
                'updated_at'               => now(),
            ]);
        }
    
        DB::commit();
        //increase total_members  and target_amount in Groups table
        if ($accountType->id == 2) {
            $group = Group::find($request->input('group_id'));
            $group->total_members += 1;
            $group->target_amount += $group->monthly_contribution;
            $group->save();
        }
    
        if ($savingsaccount->id > 0 && $transaction->id > 0) {
            if (!$request->ajax()) {
                return redirect()->route('savings_accounts.create')->with('success', _lang('Saved Successfully'));
            } else {
                return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $savingsaccount, 'table' => '#savings_accounts_table']);
            }
        }
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $savingsaccount = SavingsAccount::withoutGlobalScopes(['status'])->find($id);
        if (!$request->ajax()) {
            return view('backend.savings_accounts.view', compact('savingsaccount', 'id'));
        } else {
            return view('backend.savings_accounts.modal.view', compact('savingsaccount', 'id'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $savingsaccount = SavingsAccount::withoutGlobalScopes(['status'])->find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.savings_accounts.modal.edit', compact('savingsaccount', 'id'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'account_number'     => [
                'required',
                Rule::unique('savings_accounts')->ignore($id),
            ],
            'member_id'          => 'required',
            'savings_product_id' => 'required',
            'status'             => 'required',
            'opening_balance'    => 'required|numeric',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('savings_accounts.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $savingsaccount                     = SavingsAccount::withoutGlobalScopes(['status'])->find($id);
        $savingsaccount->account_number     = $request->input('account_number');
        $savingsaccount->member_id          = $request->input('member_id');
        $savingsaccount->savings_product_id = $request->input('savings_product_id');
        $savingsaccount->status             = $request->input('status');
        $savingsaccount->opening_balance    = $request->input('opening_balance');
        $savingsaccount->description        = $request->input('description');
        $savingsaccount->updated_user_id    = auth()->id();

        $savingsaccount->save();

        if (!$request->ajax()) {
            return redirect()->route('savings_accounts.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $savingsaccount, 'table' => '#savings_accounts_table']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $savingsaccount = SavingsAccount::withoutGlobalScopes(['status'])->find($id);

        if ($savingsaccount->savings_product_id == 2) {
            // Use the destroy function route for group members
            $groupMembers = DB::table('group_members')->where('savings_account_id', $savingsaccount->id)->get();
            foreach ($groupMembers as $groupMember) {
                app('App\Http\Controllers\GroupMemberController')->destroy($groupMember->id);
            }
        }

        $savingsaccount->delete();
        return redirect()->route('savings_accounts.index')->with('success', _lang('Deleted Successfully'));
    }

    public function get_deposit_account_by_member_id($member_id)
    {
        $savingsaccounts = SavingsAccount::with(['savings_type', 'savings_type.currency'])->where('member_id', $member_id)->get();
        return response()->json(['accounts' => $savingsaccounts]);
    }

    public function get_account_by_member_id($member_id)
    {
        $savingsaccounts = SavingsAccount::with(['savings_type', 'savings_type.currency'])->where('member_id', $member_id)
        ->whereHas('savings_type', function($savings){
            $savings->where('id', 1);
        })
        ->get();
        return response()->json(['accounts' => $savingsaccounts]);
    }

    // get group savings account by member_id
    public function get_group_account_by_savings_account_id($member_id)
    {
        $groupsavingsaccounts = SavingsAccount::with(['savings_type', 'savings_type.currency'])
            ->where('id', $member_id)
            ->whereHas('savings_type', function ($query) {
                $query->where('id', 2);
            })
            ->get();
        return response()->json(['groupaccounts' => $groupsavingsaccounts]);
    }
    public function get_group_account_by_member_id($member_id)
    {
        $groupsavingsaccounts = SavingsAccount::with(['savings_type', 'savings_type.currency'])
            ->where('member_id', $member_id)
            ->whereHas('savings_type', function ($query) {
                $query->where('id', 2);
            })
            ->get();
        return response()->json(['groupaccounts' => $groupsavingsaccounts]);
    }

}
