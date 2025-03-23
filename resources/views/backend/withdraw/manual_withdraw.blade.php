@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-8 offset-lg-2">
		<div class="card">
			<div class="card-header">
				<h4 class="header-title text-center">{{ _lang('Withdraw Money') }}</h4>
			</div>
			<div class="card-body">
				<form method="post" class="validate" autocomplete="off" action="{{ route('withdraw.manual_withdraw', $withdraw_method->id) }}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="row p-2">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Date') }}</label>
								<input type="text" class="form-control datetimepicker" name="trans_date" value="{{ old('trans_date', now()) }}"
										required>
							</div>
						</div>

						<!-- Show Group Selection if Withdraw Method ID == 2 -->
						@if($withdraw_method->id == 2)
						<div class="col-md-12">
						   <div class="form-group">
								<label class="control-label">{{ _lang('Select Group') }}</label>
								<select class="form-control select2" data-selected="{{ old('group_id') }}" name="group_id" id="group_id">
									<option value="">{{ _lang('Select One') }}</option>
									@foreach(\App\Models\Group::all() as $group)
										<option value="{{ $group->id }}">{{ $group->group_name }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Select Member from Group') }}</label>
								<select class="form-control auto-select select2" name="member_id" id="group_member_id" required>
									<option value="">{{ _lang('Select Group First') }}</option>
									@foreach(\App\Models\GroupMember::where('group_id', old('group_id'))->get() as $group_member)
										<option value="{{ $group_member->savings_account_id }}" data-account-id="{{ $group_member->savings_account_id }}">
											{{ $group_member->member->first_name.' '.$group_member->member->last_name.' ('.$group_member->member->member_no.')' }}
										</option>
									@endforeach
								</select>
							</div>

						</div>
                        <div class="col-md-6">
						    <div class="form-group">
								<label class="control-label">{{ _lang('Select Member Account Number') }}</label>
								<select class="form-control select2 auto-select" data-selected="{{ old('debit_account') }}" name="debit_account" id="debit_account1" required>
								
								</select>
							</div>
						</div>

						@else
						<div class="col-md-6">
						   <div class="form-group">
								<label class="control-label">{{ _lang('Member') }}</label>
								<select class="form-control auto-select select2" data-selected="{{ old('member_id') }}" name="member_id" id="member_id" required>
									<option value="">{{ _lang('Select One') }}</option>
									@foreach(\App\Models\Member::all() as $member)
										<option value="{{ $member->id }}">{{ $member->first_name.' '.$member->last_name }} ({{ $member->member_no }})</option>
									@endforeach
								</select>
							</div>	
						</div>
						<div class="col-md-6">
						    <div class="form-group">
								<label class="control-label">{{ _lang('Account Number') }}</label>
								<select class="form-control select2 auto-select" data-selected="{{ old('debit_account') }}" name="debit_account" id="debit_account" required>
								@if(old('member_id') != '')
									   		@foreach(\App\Models\SavingsAccount::where('member_id', old('member_id'))->get() as $account)
											<option value="{{ $account->id }}">{{ $account->account_number }} ({{ $account->savings_type->name.' - '.$account->savings_type->currency->name }})</option>
											@endforeach
									   @endif
								</select>
							</div>
						</div>
						@endif
						

						<div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Amount') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="account-currency">{{ $withdraw_method->currency->name }}</span>
                                    </div>
                                    <input type="text" class="form-control float-field" id="amount" name="amount" value="{{ old('amount') }}" required>
                                </div>
								<p class="text-danger" id="error-msg"></p>
                            </div>
                        </div>
                      <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Converted Amount') }} ({{ _lang('Charge Applied') }})</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="gateway-currency">{{ $withdraw_method->currency->name }}</span>
                                    </div>
                                    <input type="text" class="form-control float-field" id="converted_amount" name="converted_amount" value="{{ old('converted_amount') }}" readonly>
                                </div>
                            </div>
                        </div>
                      <div class="col-lg-12 my-4">						
                            <div class="table-responsive">
                                <table id="charge-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="text-center bg-light">{{ _lang('Limits & Charges') }}</th>
                                        </tr>
                                        <tr>
                                            <th>{{ _lang('Amount Limit') }}</th>
                                            <th>{{ _lang('Charge') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($withdraw_method->chargeLimits()->count() > 0)
                                            @foreach($withdraw_method->chargeLimits as $chargeLimit)
                                            <tr>
                                                <td>{{ $withdraw_method->currency->name.' '.$chargeLimit->minimum_amount }} - {{ $withdraw_method->currency->name.' '.$chargeLimit->maximum_amount }}</td>
                                                <td>{{ decimalPlace($chargeLimit->fixed_charge, currency($withdraw_method->currency->name)) }} + {{ $chargeLimit->charge_in_percentage }}%</td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>				
							</div>
						</div>
                      @if($withdraw_method->requirements)
						@foreach($withdraw_method->requirements as $requirement)
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ $requirement }}</label>
								<input type="text" class="form-control" name="requirements[{{ str_replace(' ', '_', $requirement) }}]" value="{{ old('requirements.'.str_replace(' ', '_', $requirement)) }}" required>
							</div>
						</div>
						@endforeach
						@endif

						@if($withdraw_method->descriptions != '')
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Instructions') }}</label>
								<div class="border rounded p-2">{!! xss_clean($withdraw_method->descriptions) !!}</div>
							</div>
						</div>
						@endif

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Description') }}</label>
								<textarea class="form-control" name="description">{{ old('description') }}</textarea>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Attachment') }}</label>
								<input type="file" class="form-control dropify" name="attachment">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<button type="submit" id="submit-btn" class="btn btn-primary btn-block"><i class="ti-check-box"></i>&nbsp;{{ _lang('Submit') }}</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
    </div>
</div>
@endsection

@section('js-script')
<script>
(function ($) {
	"use strict";
	var currency = $('#debit_account').find(':selected').data('currency');
	$("#account-currency").html(currency);

	$(document).on('change','#debit_account', function(){
		var currency = $(this).find(':selected').data('currency');
		$("#account-currency").html(currency);
		$("#amount").keyup();
	});

	$(document).on('change','#member_id',function(){
		var member_id = $(this).val();
		if(member_id != ''){
			$.ajax({
				url: "{{ url('admin/savings_accounts/get_account_by_member_id/') }}/" + member_id,
				success: function(data){
					var json = JSON.parse(JSON.stringify(data));
					$("#debit_account").html('');
					$.each(json['accounts'], function(i, account) {
						$("#debit_account").append(`<option value="${account.id}">${account.account_number} (${account.savings_type.name} - ${account.savings_type.currency.name})</option>`);
					});
				}
			});
		}
	});

	$(document).on('change','#group_id', function(){
		var group_id = $(this).val();
		if(group_id != ''){
			$.ajax({
				url: "{{ url('admin/group_members/get_members_by_group/') }}/" + group_id,
				success: function(data){
					var json = JSON.parse(JSON.stringify(data));
					$("#group_member_id").html('');

					if (json['members'].length > 0) {
						$.each(json['members'], function(i, member) {
							$("#group_member_id").append(`<option value="${member.savings_account_id}">${member.first_name} ${member.last_name} (${member.payout_position_number})</option>`);
						});

						// Auto-select the first member
						var firstMemberId = json['members'][0].savings_account_id;
						$("#group_member_id").val(firstMemberId).trigger('change');

					}
				}
			});
		}
	});



	$(document).on('change','#group_member_id', function(){
		var member_id = $(this).val();
		if(member_id != ''){
			$.ajax({
				url: "{{ url('admin/savings_accounts/get_group_account_by_savings_account_id/') }}/" + member_id,
				success: function(data){
					var json = JSON.parse(JSON.stringify(data));
					$("#debit_account1").html('');

					if (json['groupaccounts'].length > 0) {
						$.each(json['groupaccounts'], function(i, groupaccount) {
							$("#debit_account1").append(`<option value="${groupaccount.id}">${groupaccount.account_number} (${groupaccount.savings_type.name} - ${groupaccount.savings_type.currency.name})</option>`);
						});

						// Auto-select the first available account
						var firstAccountId = json['groupaccounts'][0].id;
						$("#debit_account1").val(firstAccountId);
					}
				}
			});
		}
	});


	$(document).on('keyup','#amount', function(){
		var from = $("#account-currency").html();
		var to = $("#gateway-currency").html();
		var amount = $(this).val();

		if($("#debit_account").val() == ''){
			Swal.fire(
				'{{ _lang('Alert') }}',
				'{{ _lang('Please select debit account first !') }}',
				'warning'
			);
			$(this).val('');
			return;
		}

		if(amount != ''){
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$.ajax({
				method: "POST",
				url: '{{ route('transfer.get_final_amount') }}',
				data: {'from' : from, 'to' : to, 'amount' : amount, 'type' : 'manual_withdraw', 'id' : '{{ $withdraw_method->id }}' },
				beforeSend: function(){
					$("#submit-btn").prop('disabled', true);
				},success: function(data){
					var json = JSON.parse(JSON.stringify(data));

					if(json['result'] == true){
						$("#converted_amount").val(parseFloat(json['amount']).toFixed(2));
						$("#error-msg").html('');
						$("#submit-btn").prop('disabled', false);
					}else{
						$("#converted_amount").val('');
						$("#error-msg").html(json['message']);
					}                 
				}
			});
		}else{
			$("#converted_amount").val('');
		}
	});

})(jQuery);

</script>
@endsection



