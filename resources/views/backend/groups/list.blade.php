@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card no-export">
            <div class="card-header d-flex align-items-center">
                <span class="panel-title">{{ _lang('Groups') }}</span>
                <a class="btn btn-primary btn-xs ml-auto ajax-modal" data-title="{{ _lang('Add New Group') }}" href="{{ route('groups.create') }}">
                    <i class="ti-plus"></i>&nbsp;{{ _lang('Add New Group') }}
                </a>
            </div>
            <div class="card-body">
                <table id="groups_table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ _lang('Group Name') }}</th>
                            <th>{{ _lang('Number of Members') }}</th>
                            <th>{{ _lang('Monthly Contribution Amount') }}</th>
                            <th>{{ _lang('Total Contributed Amount') }}</th>
                            <th>{{ _lang('Number of Payouts') }}</th>
                            <th>{{ _lang('Status') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js-script')
<script>
(function ($) {
    "use strict";

    var groups_table = $('#groups_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ url('admin/groups/get_table_data') }}',
        "columns": [
            { data: 'name', name: 'name' },
            { data: 'total_members', name: 'total_members', searchable: false, orderable: false }, // Match 'total_members'
            { data: 'monthly_contribution', name: 'monthly_contribution' }, // Match 'total_contributions'
            { data: 'group_members_sum_total_contributed', name: 'total_contributed' }, 
            { data: 'total_payouts', name: 'total_payouts' }, // Match 'total_payouts'
            { data: 'status', name: 'status' },
            { data: "action", name: "action", orderable: false, searchable: false },
        ],
        responsive: true,
        "bStateSave": true,
        "bAutoWidth": false,
        "ordering": false,
        "language": {
            "decimal": "",
            "emptyTable": "{{ _lang('No Data Found') }}",
            "info": "{{ _lang('Showing') }} _START_ {{ _lang('to') }} _END_ {{ _lang('of') }} _TOTAL_ {{ _lang('Entries') }}",
            "infoEmpty": "{{ _lang('Showing 0 To 0 Of 0 Entries') }}",
            "infoFiltered": "(filtered from _MAX_ total entries)",
            "paginate": {
                "first": "{{ _lang('First') }}",
                "last": "{{ _lang('Last') }}",
                "previous": "<i class='ti-angle-left'></i>",
                "next": "<i class='ti-angle-right'></i>",
            }
        }
    });

    $(document).on("ajax-screen-submit", function () {
        groups_table.draw();
    });

})(jQuery);
</script>
@endsection
