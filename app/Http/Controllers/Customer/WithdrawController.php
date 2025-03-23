<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use App\Models\WithdrawMethod;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\WithdrawRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WithdrawController extends Controller
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
	public function manual_methods()
	{
		$withdraw_methods = WithdrawMethod::where('status', 1)->get();
		return view('backend.withdraw.manual_methods', compact('withdraw_methods'));
	}

	public function manual_withdraw(Request $request, $methodId, $otp = '')
	{
		if ($request->isMethod('get')) {
			$alert_col = 'col-lg-8 offset-lg-2';
			$withdraw_method = WithdrawMethod::find($methodId);
			$accounts = SavingsAccount::with('savings_type')
				->whereHas('savings_type', function (Builder $query) {
					$query->where('allow_withdraw', 1);
				})
				->where('member_id', auth()->user()->member->id)
				->get();
			return view('backend.withdraw.manual_withdraw', compact('withdraw_method', 'accounts', 'alert_col'));
		} else if ($request->isMethod('post')) {

			

			if ($methodId == 2) {
				$request->validate(['group_id' => 'required']);
				$member_id = GroupMember::where('group_id', $request->group_id)->where('savings_account_id', $request->debit_account)->first()->member_id;
			}
			else {
				$member_id = $request->member_id;
			}
			

			$withdraw_method = WithdrawMethod::find($methodId);

			$account = SavingsAccount::where('id', $request->debit_account)
				->where('member_id', $member_id)
				->first();
			$accountType = $account->savings_type;

			//$min_amount = convert_currency($withdraw_method->currency->name, $accountType->currency->name, $withdraw_method->minimum_amount);
			//$max_amount = convert_currency($withdraw_method->currency->name, $accountType->currency->name, $withdraw_method->maximum_amount);

			//Secondary validation
			$validator = Validator::make($request->all(), [
				'debit_account' => 'required',
				'trans_date' => 'required',
				'member_id' => 'required',
				'requirements.*' => 'required',
				'amount' => "required|numeric",
				'attachment' => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx',
			]);
			if ($methodId == 2) {
				$validator->addRules(['group_id' => 'required|exists:groups,id']);

				// Find the group
				$group = Group::find($request->group_id);

				if (!$group) {
					return back()
						->with('error', _lang('Group not found'))
						->withInput();
				}

				// Find if member is eligible to receive payout
				$nextMember = GroupMember::where('group_id', $group->id)
					->where('has_received_payout', false)
					->orderBy('payout_position_number', 'asc')
					->first();

				if (!$nextMember || $nextMember->member_id != $member_id) {
					return back()
						->with('error', _lang('Member not eligible to receive payout at this time'))
						->withInput();
				}

				// Check if member total_contibuted is = or greter than group`s monthly contribution
				$totalContributed = GroupMember::where('group_id', $group->id)->where('savings_account_id', $request->member_id)->get('total_contributed');
				if ($totalContributed < $group->monthly_contribution) {
					return back()
						->with('error', _lang('Member have not contributed the required amount'))
						->withInput();
				}
			}

			/*,[
				                'amount.min' => _lang('The amount must be at least').' '.$min_amount.' '.$accountType->currency->name,
				                'amount.max' => _lang('The amount may not be greater than').' '.$max_amount.' '.$accountType->currency->name,
			*/

			if ($validator->fails()) {
				if ($request->ajax()) {
					return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
				} else {
					return back()
						->withErrors($validator)
						->withInput();
				}
			}

			//Convert account currency to gateway currency
			$convertedAdmount = convert_currency($accountType->currency->name, $withdraw_method->currency->name, $request->amount);

			$chargeLimit = $withdraw_method->chargeLimits()->where('minimum_amount', '<=', $convertedAdmount)->where('maximum_amount', '>=', $convertedAdmount)->first();

			if ($chargeLimit) {
				$fixedCharge = $chargeLimit->fixed_charge;
				$percentageCharge = ($convertedAdmount * $chargeLimit->charge_in_percentage) / 100;
				$charge = $fixedCharge + $percentageCharge;
			} else {
				//Convert minimum amount to selected currency
				$minimumAmount = convert_currency($withdraw_method->currency->name, $accountType->currency->name, $withdraw_method->chargeLimits()->min('minimum_amount'));
				$maximumAmount = convert_currency($withdraw_method->currency->name, $accountType->currency->name, $withdraw_method->chargeLimits()->max('maximum_amount'));
				return back()->with('error', _lang('Withdraw limit') . ' ' . $minimumAmount . ' ' . $accountType->currency->name . ' -- ' . $maximumAmount . ' ' . $accountType->currency->name)->withInput();
			}

			//Convert gateway currency to account currency
			$charge = convert_currency($withdraw_method->currency->name, $accountType->currency->name, $charge);

			if ($accountType->allow_withdraw == 0) {
				return back()
					->with('error', _lang('Withdraw is not allowed for') . ' ' . $accountType->name)
					->withInput();
			}

			if ($methodId == 2) {
				// Calculate total contributions from all members
				$totalContribution = GroupMember::where('group_id', $group->id)->sum('total_contributed');
				$totalReceived = GroupMember::where('group_id', $group->id)->sum('amount_received');
				$remainingBalance = $totalContribution - $totalReceived;

				
				if ($request->amount !== $group->target_amount) {
					return back()
						->with('error', _lang('You can only withdraw the target amount'))
						->withInput();
				}
				elseif ($remainingBalance < $request->amount) {
					return back()
						->with('error', _lang('Insufficient group balance'))
						->withInput();
				}
			} else {
				$account_balance = get_account_balance($request->debit_account, $member_id);
				if (($account_balance - $request->amount) < $accountType->minimum_account_balance) {
					return back()
						->with('error', _lang('Sorry Minimum account balance will be exceeded'))
						->withInput();
				}

				//Check Available Balance
				if ($account_balance < $request->amount) {
					return back()
						->with('error', _lang('Insufficient account balance'))
						->withInput();
				}
			}

			$attachment = "";
			if ($request->hasfile('attachment')) {
				$file = $request->file('attachment');
				$attachment = time() . $file->getClientOriginalName();
				$file->move(public_path() . "/uploads/media/", $attachment);
			}

			DB::beginTransaction();

			//Create Debit Transaction
			$debit = new Transaction();
			$debit->trans_date = $request->input('trans_date');
			$debit->member_id = $member_id;
			$debit->savings_account_id = $request->debit_account;
			$debit->charge = $charge;
			$debit->amount = $request->amount - $charge;
			$debit->dr_cr = 'dr';
			$debit->type = 'Withdraw';
			$debit->method = 'Manual';
			$debit->status = 2;
			$debit->created_user_id = auth()->id();
			// $debit->branch_id = auth()->user()->member->branch_id;
			$debit->description = _lang('Withdraw Money via') . ' ' . $withdraw_method->name;

			$debit->save();

			//Create Charge Transaction
			if ($charge > 0) {
				$fee = new Transaction();
				$fee->trans_date = now();
				$fee->member_id = $member_id;
				$fee->savings_account_id = $request->debit_account;
				$fee->amount = $charge;
				$fee->dr_cr = 'dr';
				$fee->type = 'Fee';
				$fee->method = 'Manual';
				$fee->status = 2;
				$fee->created_user_id = auth()->id();
				// $fee->branch_id = auth()->user()->member->branch_id;
				$fee->description = $withdraw_method->name . ' ' . _lang('Withdraw Fee');
				$fee->parent_id = $debit->id;
				$fee->save();
			}

			// Mark the member as received payout and insert the amount received and date
			if ($methodId == 2) {
				$nextMember->has_received_payout = true;
				$nextMember->amount_received = $request->amount;
				$nextMember->has_received_payout_date = now();
				$nextMember->save();
			}



			// Reset all contributions (since the money has been given out)
			// GroupMember::where('group_id', $group->id)->update(['total_contributed' => 0]);



			// $withdrawRequest = new WithdrawRequest();
			// $withdrawRequest->member_id = $member_id;
			// $withdrawRequest->method_id = $methodId;
			// $withdrawRequest->debit_account_id = $request->debit_account;
			// $withdrawRequest->amount = $request->amount;
			// $withdrawRequest->converted_amount = convert_currency($withdrawRequest->account->savings_type->currency->name, $withdraw_method->currency->name, $request->amount);
			// $withdrawRequest->description = $request->description;
			// $withdrawRequest->requirements = json_encode($request->requirements);
			// $withdrawRequest->attachment = $attachment;
			// $withdrawRequest->transaction_id = $debit->id;
			// $withdrawRequest->save();

			DB::commit();

			if (!$request->ajax()) {
				return redirect()->route('withdraw.manual_methods')->with('success', _lang('Money Withdrawn Successfully'));
			} else {
				return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Money Withdrawn Successfully')]);
			}
		}
	}
}
