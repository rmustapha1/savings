<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\SavingsAccount;
use App\Models\SavingsProduct;
use App\Models\Transaction;
use App\Notifications\DepositMoney;
use App\Notifications\WithdrawMoney;
use App\Models\GroupMember;
use App\Models\Group;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class TransactionController extends Controller
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
		return view('backend.transaction.list');
	}

	public function get_table_data()
	{

		$transactions = Transaction::select('transactions.*')
			->with(['member', 'account', 'account.savings_type'])
			->orderBy("transactions.trans_date", "desc");

		return Datatables::eloquent($transactions)
			->editColumn('member.first_name', function ($transactions) {
				return $transactions->member->first_name . ' ' . $transactions->member->last_name;
			})
			->editColumn('dr_cr', function ($transactions) {
				return strtoupper($transactions->dr_cr);
			})
			->editColumn('status', function ($transactions) {
				return transaction_status($transactions->status);
			})
			->editColumn('amount', function ($transaction) {
				$symbol = $transaction->dr_cr == 'dr' ? '-' : '+';
				$class = $transaction->dr_cr == 'dr' ? 'text-danger' : 'text-success';
				return '<span class="' . $class . '">' . $symbol . ' ' . decimalPlace($transaction->amount, currency($transaction->account->savings_type->currency->name)) . '</span>';
			})
			->editColumn('type', function ($transaction) {
				return ucwords(str_replace('_', ' ', $transaction->type));
			})
			->filterColumn('member.first_name', function ($query, $keyword) {
				$query->whereHas('member', function ($query) use ($keyword) {
					return $query->where("first_name", "like", "{$keyword}%")
						->orWhere("last_name", "like", "{$keyword}%");
				});
			}, true)
			->addColumn('action', function ($transaction) {
				return '<div class="dropdown text-center">'
					. '<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
					. '&nbsp;</button>'
					. '<div class="dropdown-menu">'
					. '<a class="dropdown-item" href="' . route('transactions.edit', $transaction['id']) . '"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a>'
					. '<a class="dropdown-item" href="' . route('transactions.show', $transaction['id']) . '"><i class="ti-eye"></i>  ' . _lang('View') . '</a>'
					. '<form action="' . route('transactions.destroy', $transaction['id']) . '" method="post">'
					. csrf_field()
					. '<input name="_method" type="hidden" value="DELETE">'
					. '<button class="dropdown-item btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
					. '</form>'
					. '</div>'
					. '</div>';
			})
			->setRowId(function ($transaction) {
				return "row_" . $transaction->id;
			})
			->rawColumns(['action', 'status', 'amount'])
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
			return view('backend.transaction.create');
		} else {
			return view('backend.transaction.modal.create');
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
		    // Determine if it's group or individual based on submitted savings_product_id (1 = Individual, 2 = Group)
			$isGroup = ($request->input('savings_product_id') == 2);

			// Validation rules
			$rules = [
				'trans_date' => 'required|date',
				'savings_product_id' => 'required',
				'amount' => 'required|numeric|min:0',
				'dr_cr' => 'required',
				'type' => 'required',
				'collector_id' => 'required',
				'status' => 'required',
				'description' => 'required',
			];
		
			if ($isGroup) {
				$rules['group_id'] = 'required';  // Only for group savings
				$rules['savings_account_id'] = 'required';  // Group member account
			} else {
				$rules['member_id'] = 'required';  // Only for individual savings
				$rules['savings_account_id'] = 'required';  // Individual member's account
			}

		$validator = Validator::make($request->all(), $rules, [
			'dr_cr.in' => 'Transaction must have a debit or credit',
		]);


		if ($validator->fails()) {
			if ($request->ajax()) {
				return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
			} else {
				return back()
					->withErrors($validator)
					->withInput();
			}
		}

		$accountType = $request->savings_product_id == 2 ? SavingsProduct::find($request->savings_product_id) : SavingsAccount::find($request->savings_account_id)->savings_type;

		if (!$accountType) {
			return back()
				->with('error', _lang('Account type not found'))
				->withInput();
		}

		if ($request->dr_cr == 'dr') {
			if ($accountType->allow_withdraw == 0) {
				return back()
					->with('error', _lang('Withdraw is not allowed for') . ' ' . $accountType->name)
					->withInput();
			}

			$account_balance = get_account_balance($request->savings_account_id, $request->member_id);
			if (($account_balance - $request->amount) < $accountType->minimum_account_balance) {
				return back()
					->with('error', _lang('Sorry Minimum account balance will be exceeded'))
					->withInput();
			}

			if ($account_balance < $request->amount) {
				return back()
					->with('error', _lang('Insufficient account balance'))
					->withInput();
			}
		} else {
			if ($request->amount < $accountType->minimum_deposit_amount) {
				return back()
					->with('error', _lang('You must deposit minimum') . ' ' . $accountType->minimum_deposit_amount . ' ' . $accountType->currency->name)
					->withInput();
			}
		}
		if ($accountType->id == 2) {
			$member_id = GroupMember::where('group_id', $request->group_id)->where('savings_account_id', $request->savings_account_id)->first()->member_id;
		}
		else {
			$member_id = $request->member_id;
		}

		$transaction = new Transaction();
		$transaction->trans_date = $request->input('trans_date');
		$transaction->member_id = $member_id;
		$transaction->savings_account_id = $request->input('savings_account_id');
		$transaction->amount = $request->input('amount');
		$transaction->dr_cr = $request->dr_cr == 'dr' ? 'dr' : 'cr';
		$transaction->type = ucwords($request->type);
		$transaction->method = 'Manual';
		$transaction->status = $request->input('status');
		$transaction->description = $request->input('description');
		$transaction->created_user_id = auth()->id();
		$transaction->collector_id = $request->input('collector_id');

		// save group member contribution
		if ($accountType->id == 2) {
			$group_id = $request->group_id;
			$group_member = GroupMember::where('group_id', $group_id)->where('savings_account_id', $request->savings_account_id)->first();
			$group_member->total_contributed += $request->amount;
			$group_member->save();
		}

		$transaction->save();

		if ($transaction->dr_cr == 'dr') {
			try {
				$transaction->member->notify(new WithdrawMoney($transaction));
			} catch (\Exception $e) {
			}
		} else if ($transaction->dr_cr == 'cr') {
			try {
				$transaction->member->notify(new DepositMoney($transaction));
			} catch (\Exception $e) {
			}
		}

		if (!$request->ajax()) {
			return redirect()->route('transactions.index')->with('success', _lang('Saved Successfully'));
		} else {
			return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $transaction, 'table' => '#transactions_table']);
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
		$transaction = Transaction::find($id);
		if (!$request->ajax()) {
			return view('backend.transaction.view', compact('transaction', 'id'));
		} else {
			return view('backend.transaction.modal.view', compact('transaction', 'id'));
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
		$transaction = Transaction::find($id);
		if (!$request->ajax()) {
			return view('backend.transaction.edit', compact('transaction', 'id'));
		} else {
			return view('backend.transaction.modal.edit', compact('transaction', 'id'));
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
  
		    // Determine if it's group or individual based on submitted savings_product_id (1 = Individual, 2 = Group)
			$isGroup = ($request->input('savings_product_id') == 2);

			// Validation rules
			$rules = [
				'trans_date' => 'required|date',
				'savings_product_id' => 'required',
				'amount' => 'required|numeric|min:0',
				'dr_cr' => 'required',
				'type' => 'required',
				'status' => 'required',
				'description' => 'required',
			];
		
			if ($isGroup) {
				$rules['group_id'] = 'required';  // Only for group savings
				$rules['savings_account_id'] = 'required';  // Group member account
			} else {
				$rules['member_id'] = 'required';  // Only for individual savings
				$rules['savings_account_id'] = 'required';  // Individual member's account
			}

			if($request->dr_cr == 'cr') {
				$rules['collector_id'] = 'required';
			} else {
				$rules['collector_id'] = null; // Only for Withdrawal
			}

			$validator = Validator::make($request->all(), $rules, [
				'dr_cr.in' => 'Transaction must have a debit or credit',
			]);

    if ($validator->fails()) {
        if ($request->ajax()) {
            return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
        } else {
            return redirect()->route('transactions.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }
    }

    $transaction = Transaction::find($id);
    $account = SavingsAccount::find($request->savings_account_id);

    if (!$account) {
        return back()
            ->with('error', _lang('Account not found'))
            ->withInput();
    }

    $accountType = $account->savings_type;
    $isGroupTransaction = GroupMember::where('savings_account_id', $request->savings_account_id)->exists();

    // Validate withdrawal conditions for both individual and group accounts
    if ($request->dr_cr == 'dr') {
        if ($accountType->allow_withdraw == 0) {
            return back()
                ->with('error', _lang('Withdraw is not allowed for') . ' ' . $accountType->name)
                ->withInput();
        }

        $account_balance = get_account_balance($request->savings_account_id, $request->member_id);
        $previousAmount = $request->member_id == $transaction->member_id ? $transaction->amount : 0;

        if ((($account_balance + $previousAmount) - $request->amount) < $accountType->minimum_account_balance) {
            return back()
                ->with('error', _lang('Sorry, minimum account balance will be exceeded'))
                ->withInput();
        }

        if (($account_balance + $previousAmount) < $request->amount) {
            return back()
                ->with('error', _lang('Insufficient account balance'))
                ->withInput();
        }
    } else {
        if ($request->amount < $accountType->minimum_deposit_amount) {
            return back()
                ->with('error', _lang('You must deposit at least') . ' ' . $accountType->minimum_deposit_amount . ' ' . $accountType->currency->name)
                ->withInput();
        }
    }

    // Update transaction
    $transaction->trans_date = $request->input('trans_date');
    $transaction->member_id = $request->input('member_id');
    $transaction->savings_account_id = $request->input('savings_account_id');
    $transaction->amount = $request->input('amount');
    $transaction->status = $request->input('status');
    $transaction->description = $request->input('description');
	$transaction->collector_id = $request->input('collector_id');
    $transaction->updated_user_id = auth()->id();
    $transaction->save();

    // If it's a group transaction, update the GroupMember table
    if ($isGroupTransaction) {
        $groupMember = GroupMember::where('savings_account_id', $request->savings_account_id)->first();
        
        if ($groupMember) {
            $groupMember->total_contributed = $request->amount;
            $groupMember->updated_at = now();
            $groupMember->save();
        }
    }

    if (!$request->ajax()) {
        return redirect()->route('transactions.index')->with('success', _lang('Updated Successfully'));
    } else {
        return response()->json([
            'result' => 'success',
            'action' => 'update',
            'message' => _lang('Updated Successfully'),
            'data' => $transaction,
            'table' => '#transactions_table'
        ]);
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
		DB::beginTransaction();

		$transaction = Transaction::find($id);

		if ($transaction->loan_id != null) {
			$loan = Loan::find($transaction->loan_id);
			if ($loan->status == 2) {
				return back()->with('error', _lang('Sorry, this transaction is associated with a loan !'));
			}
		}

		$transaction->delete();

		DB::commit();

		return redirect()->route('transactions.index')->with('success', _lang('Deleted Successfully'));
	}
}
