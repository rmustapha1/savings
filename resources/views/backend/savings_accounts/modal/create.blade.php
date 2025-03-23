<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('savings_accounts.store') }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="row px-2">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account Number') }}</label>                        
                <input type="text" class="form-control" name="account_number" id="account_number" value="{{ old('account_number') }}" required readonly>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Member') }}</label>                        
                <select class="form-control select2" name="member_id" id="member_id" required>
                    <option value="">{{ _lang('Select Member') }}</option>
                    @foreach(\App\Models\Member::all() as $member)
                        <option value="{{ $member->id }}">{{ $member->first_name.' '.$member->last_name }} ({{ $member->member_no }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account Type') }}</label>                        
                <select class="form-control select2" name="savings_product_id" id="savings_product_id" required>
                    <option value="">{{ _lang('Select One') }}</option>
                    @foreach(App\Models\SavingsProduct::active()->get() as $product)
                        <option value="{{ $product->id }}" data-account-number="{{ $product->account_number_prefix.$product->starting_account_number }}">
                            {{ $product->name }} ({{ $product->currency->name }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- New "Select Group" Field (Appears Only for Group Savings Account) -->
        <div class="col-md-12" id="group_field" style="display: none;">
            <div class="form-group">
                <label class="control-label">{{ _lang('Select Group') }}</label>                        
                <select class="form-control select2" name="group_id" id="group_id">
                    <option value="">{{ _lang('Select Group') }}</option>
                    @foreach(\App\Models\Group::all() as $group)
                        <option value="{{ $group->id }}">
							{{ $group->group_name }} (GHS{{ $group->monthly_contribution}} Monthly)</option>
                    @endforeach
                </select>
            </div>
        </div>
		<!-- Group Member Payout Position Number (Appears Only for Group Savings Account) -->
		 <div class="col-md-12" id="payout_position" style="display: none;">
            <div class="form-group">
                <label class="control-label">{{ _lang('Payout Position Number') }}</label>                        
                <input type="text" class="form-control float-field" name="payout_position_number" id="payout_position_number" value="{{ old('payout_position_number') }}" readonly>
            </div>
        </div>
        <!-- End Group Member Payout Position Number -->
        
		
		<div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Status') }}</label>                        
                <select class="form-control auto-select" data-selected="{{ old('status',1) }}" name="status" required>
                    <option value="1">{{ _lang('Active') }}</option>
                    <option value="0">{{ _lang('Deactivate') }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Opening Balance') }}</label>                        
                <input type="text" class="form-control float-field" name="opening_balance" value="{{ old('opening_balance') }}" required>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Collector') }}</label>
                    <select class="form-control select2" name="collector_id" id="collector_id" required>
                        <option value="">{{ _lang('Select One') }}</option>
                        @foreach(\App\Models\User::where('role_id', '5')->get() as $collector)
                            <option value="{{ $collector->id }}">{{ $collector->name }}</option>
                        @endforeach
                    </select>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Description') }}</label>                        
                <textarea class="form-control" name="description">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="ti-check-box"></i>&nbsp;{{ _lang('Submit') }}</button>
            </div>
        </div>
    </div>
</form>

<script>
(function ($) {
    $(document).on('change','#savings_product_id', function(){
        var selectedProductId = $(this).val();
        var accountNumber = $(this).find(':selected').data('account-number');

        if(selectedProductId) {
            if(accountNumber) {
                $("#account_number").val(accountNumber);
            } else {
                Swal.fire({
                    text: "{{ _lang('Please set starting account number to your selected account type before creating new account!') }}",
                    icon: "error",
                    confirmButtonColor: "#e74c3c",
                    confirmButtonText: "{{ _lang('Close') }}",
                });
            }

            // Show "Select Group" field if savings_product_id == 2 (Group Savings Account)
            if (selectedProductId == "2") {
                $('#group_field').show();
                $('#group_id').attr('required', true);
				$('#payout_position').show();
				$('#payout_position_number').attr('required', true);
            } else {
                $('#group_field').hide();
                $('#group_id').removeAttr('required');
				$('#payout_position').hide();
				$('#payout_position_number').removeAttr('required');
            }
        } else {
            $("#account_number").val('');
            $('#group_field').hide();
            $('#group_id').removeAttr('required');
			$('#payout_position').hide();
			$('#payout_position_number').removeAttr('required');
            $('#member_id').attr('required', true);
        }
    });

    // Auto-select position payout positions based on the group members count
$(document).on('change', '#group_id', function () {
    var savings_account_id = $(this).val();
    var group_id = $('#group_id').val();

    if (savings_account_id !== '') {
        $.getJSON("{{ route('group_members.get_payout_position_number', ':group_id') }}"
        .replace(':group_id', group_id))
        .done(function (data) {
            var next_position = data.highest_payout_position ? data.highest_payout_position + 1 : 1;
            $("#payout_position_number").val(next_position);
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.error("Error fetching payout position:", textStatus, errorThrown);
            alert("Failed to fetch payout position. Please try again.");
        });
    }
});

})(jQuery);
</script>
