@extends('layouts.app')

@section('content')
@php $type = isset($_GET['type']) ? $_GET['type'] : ''; @endphp
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				@if($type != '')
				<h4 class="header-title">{{ $type == 'deposit' ? _lang('Deposit Money') : _lang('Withdraw Money') }}</h4>
				@else
				<h4 class="header-title">{{ _lang('New Transaction')}}</h4>
				@endif
			</div>
			<div class="card-body">
			    <form method="post" class="validate" autocomplete="off" action="{{ route('transactions.store') }}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-lg-8">
							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Date') }}</label>
								<div class="col-xl-9">
									<input type="text" class="form-control datetimepicker" name="trans_date" value="{{ old('trans_date', now()) }}"
										required>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Deposit Type') }}</label>                        
								<div class="col-xl-9">
									<select class="form-control select2" name="savings_product_id" id="deposit_type" required>
										<option value="">{{ _lang('Select One') }}</option>
										@foreach(App\Models\SavingsProduct::active()->get() as $product)
											<option value="{{ $product->id }}" 
												data-type="{{ $product->id == 2 ? 'group' : 'individual' }}">
												{{ $product->name }} ({{ $product->currency->name }})
											</option>
										@endforeach
									</select>
								</div>
							</div>

								<!-- Group Fields -->
							<div id="group_fields" style="display: none;">
								<div class="form-group row">
									<label class="col-xl-3 col-form-label">{{ _lang('Select Group') }}</label>
									<div class="col-xl-9">
										<select class="form-control select2" name="group_id" id="group_id">
											<option value="">{{ _lang('Select Group') }}</option>
											@foreach(\App\Models\Group::all() as $group)
												<option value="{{ $group->id }}">
													{{ $group->group_name }} (GHS{{ $group->monthly_contribution }} Monthly)
												</option>
											@endforeach
										</select>
									</div>
							    </div>

								<div class="form-group row" id="group_member_field">
									<label class="col-xl-3 col-form-label">{{ _lang('Select Member from Group') }}</label>
									<div class="col-xl-9">
										<select class="form-control select2" name="group_member_id" id="group_member_id">
											<option value="">{{ _lang('Select Group First') }}</option>
										</select>
									</div>
								</div>

								<div class="form-group row" id="account_number_field">
									<label class="col-xl-3 col-form-label">{{ _lang('Select Member Account Number') }}</label>
									<div class="col-xl-9">
										<select class="form-control select2" name="savings_account_id" id="debit_account1">
										</select>
									</div>
								</div>
								</div>

								<!-- Individual Fields -->
							<div id="individual_fields" style="display: none;">
								<div class="form-group row">
									<label class="col-xl-3 col-form-label">{{ _lang('Member') }}</label>
									<div class="col-xl-9">
										<select class="form-control select2" name="member_id" id="member_id">
											<option value="">{{ _lang('Select One') }}</option>
											@foreach(\App\Models\Member::all() as $member)
												<option value="{{ $member->id }}">{{ $member->first_name.' '.$member->last_name }} ({{$member->member_no}})</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-xl-3 col-form-label">{{ _lang('Account Number') }}</label>
									<div class="col-xl-9">
										<select class="form-control select2" name="savings_account_id" id="savings_account_id">
										</select>
									</div>
								</div>
							</div>


							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Amount') }}</label>
								<div class="col-xl-9">
									<input type="text" class="form-control float-field" name="amount" value="{{ old('amount') }}" required>
								</div>
							</div>

							@if($type == '')
							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Debit/Credit') }}</label>
								<div class="col-xl-9">
									<select class="form-control" name="dr_cr" id="dr_cr" required>
										<option value="">{{ _lang('Select One') }}</option>
										<option value="dr">{{ _lang('Debit') }}</option>
										<option value="cr">{{ _lang('Credit') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Transaction Types') }}</label>
								<div class="col-xl-9">
									<select class="form-control select2" name="type" id="transaction_type" required>
										<option value="">{{ _lang('Select One') }}</option>
									</select>
								</div>
							</div>
							@else
							<input type="hidden" name="dr_cr" value="{{ $type == 'deposit' ? 'cr' : 'dr' }}">
							<input type="hidden" name="type" value="{{ $type }}">
							@endif
							<div class="form-group row">
							<label class="col-xl-3 col-form-label">{{ _lang('Collector') }}</label>
								<div class="col-xl-9">
								<select class="form-control select2" name="collector_id" id="collector_id" required>
									<option value="">{{ _lang('Select One') }}</option>
									@foreach(\App\Models\User::where('role_id', '5')->get() as $collector)
										<option value="{{ $collector->id }}">{{ $collector->name }}</option>
									@endforeach
								</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Status') }}</label>
								<div class="col-xl-9">
									<select class="form-control auto-select" data-selected="{{ old('status', 2) }}" name="status" required>
										<option value="">{{ _lang('Select One') }}</option>
										<option value="0">{{ _lang('Pending') }}</option>
										<option value="1">{{ _lang('Cancelled') }}</option>
										<option value="2">{{ _lang('Completed') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Description') }}</label>
								<div class="col-xl-9">
									<textarea class="form-control" name="description" required>{{ old('description') }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<div class="col-xl-9 offset-xl-3">
									<button type="submit" class="btn btn-primary"><i class="ti-check-box"></i>&nbsp;{{ _lang('Submit') }}</button>
								</div>
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

	// Toggle visibility of fields based on deposit type
	$('#deposit_type').on('change', function () {
		const selectedOption = $(this).find('option:selected');
		const type = selectedOption.data('type');  // Get custom data attribute
		
		if (type === 'group') {
			$('#group_fields').show();
			$('#individual_fields').hide();
		} else {
			$('#group_fields').hide();
			$('#individual_fields').show();
		}
	});


//   Get Member Account Id (Individual Savings Account)
	$(document).on('change','#member_id',function(){
		var member_id = $(this).val();
		if(member_id != ''){
			$.ajax({
				url: "{{ url('admin/savings_accounts/get_account_by_member_id/') }}/" + member_id,
				success: function(data){
					var json = JSON.parse(JSON.stringify(data));
					$("#savings_account_id").html('');
					$.each(json['accounts'], function(i, account) {
						$("#savings_account_id").append(`<option value="${account.id}">${account.account_number} (${account.savings_type.name} - ${account.savings_type.currency.name})</option>`);
					});

				}
			});
		}
	});

	// Get Group Members by Group Id

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

// Get Group Account Id (Group Savings Account)

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


	$(document).on('change','#dr_cr',function(){
		var dr_cr = $(this).val();
		if(dr_cr != ''){
			$.ajax({
				url: "{{ url('admin/transaction_categories/get_category_by_type/') }}/" + dr_cr,
				success: function(data){
					var json = JSON.parse(JSON.stringify(data));
					$("#transaction_type").html('');
					$.each(json, function(i, category) {
						$("#transaction_type").append(`<option value="${category.value}">${category.name}</option>`);
					});

				}
			});
		}
	});

})(jQuery);
</script>
@endsection


