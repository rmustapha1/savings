<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{route('group_members.store')}}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="row px-2">

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
               <label class="control-label">{{ _lang('Account Number') }}</label>
               <select class="form-control select2 auto-select" data-selected="{{ old   ('savings_account_id') }}" name="savings_account_id" id="savings_account_id" required>
                </select>
            </div>
        </div>
        
         <!-- Group id hidden input-->
          <input type="hidden" name="group_id" id="group_id" value="{{ $group->id }}">



		<!-- Group Member Payout Position Number-->
		 <div class="col-md-12" id="payout_position">
            <div class="form-group">
                <label class="control-label">{{ _lang('Payout Position Number') }}</label>                        
                <input type="text" class="form-control float-field" name="payout_position_number" id="payout_position_number" value="{{ old('payout_position_number') }}" readonly>
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
                <button type="submit" class="btn btn-primary"><i class="ti-check-box"></i>&nbsp;{{ _lang('Submit') }}</button>
            </div>
        </div>
    </div>
</form>

<script>
     // Auto select savings account
     $(document).on('change','#member_id', function(){
		var member_id = $(this).val();
		if(member_id != ''){
			$.ajax({
				url: "{{ url('admin/savings_accounts/get_group_account_by_member_id/') }}/" + member_id,
				success: function(data){
					var json = JSON.parse(JSON.stringify(data));
                    $("#savings_account_id").html('');

					if (json['groupaccounts'].length > 0) {
						$.each(json['groupaccounts'], function(i, groupaccount) {
                            $("#savings_account_id").append(`<option value="${groupaccount.id}">${groupaccount.account_number} (${groupaccount.savings_type.name} - ${groupaccount.savings_type.currency.name})</option>`);
						});

						// Auto-select the first available account
						var firstAccountId = json['groupaccounts'][0].id;
                        $("#savings_account_id").val(firstAccountId);
					}
				}
			});
		}
	});

    
// Auto-select position payout positions based on the group members count
$(document).on('change', '#member_id', function () {
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

</script>
