<table class="table table-bordered">
	<tr><td>{{ _lang('Group Name') }}</td><td>{{ $group->group_name}}</td></tr>
	<tr><td>{{ _lang('Monthtly Contribution') }}</td><td>{{ decimalPlace($group->monthly_contribution)}}</td></tr>
	<tr><td>{{ _lang('Number of Members') }}</td><td>{{ $group->total_members}}</td></tr>

	<tr><td>{{ _lang('Total Contributed Amount') }}</td><td>{{ decimalPlace($total_contributed)}}</td></tr>
	<tr><td>{{ _lang('Total Withdrawn Amount') }}</td><td>{{ decimalPlace($amount_received)}}</td></tr>
	<tr><td>{{ _lang('Remaining Balance') }}</td><td>{{ decimalPlace($balance_remaining)}}</td></tr>
	<tr><td>{{ _lang('Number of Payouts') }}</td><td>{{ $total_payouts}}</td></tr>
	<tr><td>{{ _lang('Status') }}</td><td>{!! xss_clean(status($group->status)) !!}</td></tr>
	<tr><td>{{ _lang('Created By') }}</td><td>{{ $group->created_by->name }} ({{ $group->created_at }})</td></tr>
	<tr><td>{{ _lang('Updated By') }}</td><td>{{ $group->updated_by->name }} ({{ $group->updated_at }})</td></tr>
</table>

