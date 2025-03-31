@extends('layouts.app')

@section('content')
@php $type = isset($_GET['type']) ? $_GET['type'] : ''; @endphp
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Edit Transaction') }}</h4>
            </div>
            <div class="card-body">
                <form method="post" class="validate" autocomplete="off" action="{{ route('transactions.update', $transaction->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Date') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control datetimepicker" name="trans_date" value="{{ $transaction->getRawOriginal('trans_date') }}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Deposit Type') }}</label>
                                <div class="col-xl-9">
								@php
							// Retrieve the related savings account
							$savingsAccount = \App\Models\SavingsAccount::find($transaction->savings_account_id);

							// Get the savings product ID from the account
							$selectedProductId = $savingsAccount ? $savingsAccount->savings_product_id : null;
						@endphp

								<select class="form-control select2" name="savings_product_id" id="deposit_type" required>
									<option value="">{{ _lang('Select One') }}</option>
									@foreach(App\Models\SavingsProduct::active()->get() as $product)
										<option value="{{ $product->id }}" 
											data-type="{{ $product->id == 2 ? 'group' : 'individual' }}" 
											{{ $selectedProductId == $product->id ? 'selected' : '' }}>
											{{ $product->name }} ({{ $product->currency->name }})
										</option>
									@endforeach
								</select>
                                </div>
                            </div>

							@php
								// Retrieve group member details using savings_account_id
								$groupMember = \App\Models\GroupMember::where('savings_account_id', $transaction->savings_account_id)->first();
								$selectedGroupId = $groupMember ? $groupMember->group_id : null;
								$selectedGroupMemberId = $groupMember ? $groupMember->savings_account_id : null;
							@endphp

                            <div id="group_fields" style="display: none;">
                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Select Group') }}</label>
                                    <div class="col-xl-9">
										<select class="form-control select2" name="group_id" id="group_id">
											<option value="">{{ _lang('Select Group') }}</option>
											@foreach(\App\Models\Group::all() as $group)
												<option value="{{ $group->id }}" {{ $selectedGroupId == $group->id ? 'selected' : '' }}>
													{{ $group->group_name }} (GHS{{ $group->monthly_contribution }} Monthly)
												</option>
											@endforeach
										</select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Select Member from Group') }}</label>
                                    <div class="col-xl-9">
										<select class="form-control select2" name="group_member_id" id="group_member_id">
											<option value="">{{ _lang('Select Member') }}</option>
											@if($groupMember)
												<option value="{{ $groupMember->savings_account_id }}" selected>
													{{ $groupMember->member->first_name }} {{ $groupMember->member->last_name }} ({{ $groupMember->payout_position_number }})
												</option>
											@endif
										</select>
                                    </div>
                                </div>

								<div class="form-group row" id="account_number_field">
									<label class="col-xl-3 col-form-label">{{ _lang('Select Member Account Number') }}</label>
									<div class="col-xl-9">
										<select class="form-control select2" name="savings_account_id" id="debit_account1">
											<option value="{{ $transaction->savings_account_id }}" selected>
												{{ $transaction->account->account_number }} ({{ $transaction->account->savings_type->name }} - {{ $transaction->account->savings_type->currency->name }})
											</option>
										</select>
									</div>
								</div>
                            </div>

                            <div id="individual_fields" style="display: none;">
                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Member') }}</label>
                                    <div class="col-xl-9">
                                        <select class="form-control select2" name="member_id" id="member_id">
                                            <option value="">{{ _lang('Select One') }}</option>
                                            @foreach(App\Models\Member::all() as $member)
                                                <option value="{{ $member->id }}" {{ $transaction->member_id == $member->id ? 'selected' : '' }}>
                                                    {{ $member->first_name.' '.$member->last_name }} ({{ $member->member_no }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

								<div class="form-group row">
									<label class="col-xl-3 col-form-label">{{ _lang('Account Number') }}</label>
									<div class="col-xl-9">
										<select class="form-control select2" name="savings_account_id" id="savings_account_id">
										<option value="{{ $transaction->savings_account_id }}" selected>
												{{ $transaction->account->account_number }} ({{ $transaction->account->savings_type->name }} - {{ $transaction->account->savings_type->currency->name }})
											</option>
										</select>
									</div>
								</div>

                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Amount') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control float-field" name="amount" value="{{ old('amount', $transaction->amount) }}" required>
                                </div>
                            </div>

                            <input type="hidden" name="dr_cr" value="{{ $transaction->dr_cr }}">
                            <input type="hidden" name="type" value="{{ $transaction->type }}">

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Collector') }}</label>
                                <div class="col-xl-9">
                                    <select class="form-control select2" name="collector_id" id="collector_id" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        @foreach(App\Models\User::where('role_id', '5')->get() as $collector)
                                            <option value="{{ $collector->id }}" {{ $transaction->collector_id == $collector->id ? 'selected' : '' }}>
                                                {{ $collector->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Status') }}</label>
                                <div class="col-xl-9">
                                    <select class="form-control" name="status" required>
                                        <option value="0" {{ $transaction->status == 0 ? 'selected' : '' }}>{{ _lang('Pending') }}</option>
                                        <option value="1" {{ $transaction->status == 1 ? 'selected' : '' }}>{{ _lang('Cancelled') }}</option>
                                        <option value="2" {{ $transaction->status == 2 ? 'selected' : '' }}>{{ _lang('Completed') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Description') }}</label>
                                <div class="col-xl-9">
                                    <textarea class="form-control" name="description" required>{{ old('description', $transaction->description) }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-xl-9 offset-xl-3">
                                    <button type="submit" class="btn btn-primary"><i class="ti-check-box"></i>&nbsp;{{ _lang('Update') }}</button>
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
    function toggleFields() {
        const selectedOption = $('#deposit_type').find('option:selected');
        const type = selectedOption.data('type');

        if (type === 'group') {
            $('#group_fields').show();
            $('#individual_fields').hide();
        } else if (type === 'individual') {
            $('#group_fields').hide();
            $('#individual_fields').show();
        } else {
            $('#group_fields').hide();
            $('#individual_fields').hide();
        }
    }

    // Run on page load
    $(document).ready(function () {
        toggleFields();
    });

    // Run on change
    $('#deposit_type').on('change', function () {
        toggleFields();
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

	function loadExactGroupMember(groupId, savingsAccountId) {
        if (groupId) {
            $.ajax({
                url: "{{ url('admin/group_members/get_members_by_group/') }}/" + groupId,
                success: function (data) {
                    var json = JSON.parse(JSON.stringify(data));
                    $("#group_member_id").html('');

                    if (json['members'].length > 0) {
                        $.each(json['members'], function (i, member) {
                            if (member.savings_account_id == savingsAccountId) {
                                $("#group_member_id").append(`<option value="${member.savings_account_id}" selected>${member.first_name} ${member.last_name} (${member.payout_position_number})</option>`);
                            }
                        });
                    }
                }
            });
        }
    }

	$(document).ready(function () {
        let selectedGroupId = $("#group_id").val();
        let selectedSavingsAccountId = $("#debit_account1").val();

        if (selectedGroupId && selectedSavingsAccountId) {
            loadExactGroupMember(selectedGroupId, selectedSavingsAccountId);
        }
    });

    // Update members when group is changed
    $('#group_id').on('change', function () {
        let savingsAccountId = $("#debit_account1").val();
        loadExactGroupMember($(this).val(), savingsAccountId);
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