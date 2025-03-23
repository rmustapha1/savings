@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card no-export">
            <div class="card-header d-flex align-items-center">
                <span class="panel-title">{{$group->group_name}} {{ _lang('Group Members') }}</span>
                <!-- Add new member button -->
                <a class="btn btn-primary btn-xs ml-auto ajax-modal" data-title="{{ _lang('Add New Member') }}" href="{{route('group_members.create', $group->id)}}">
                    <i class="ti-plus"></i>&nbsp;{{ _lang('Add New Member') }}
                </a>
                <!-- Reset & shuffle button -->
                <a id="reset-button" class="btn btn-warning btn-xs ml-3" data-group-id="{{ $group->id }}">
                    <i class="ti-control-shuffle"></i>&nbsp;{{ _lang('Reset & Shuffle') }}
                </a>
            </div>
            <div class="card-body">
                <table id="group_members_table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ _lang('Member Name') }}</th>
                            <th>{{ _lang('Account Number') }}</th>
                            <th>{{ _lang('Total Contributed') }}</th>
                            <th>{{ _lang('Payout Position Number') }}</th>
                            <th>{{ _lang('Has Received Payout') }}</th>
                            <th>{{ _lang('Amount Received') }}</th>
                            <th>{{ _lang('Payout Received Date') }}</th>
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

    var group_members_table = $('#group_members_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('group_members.get_table_data', $group->id) }}',
        "columns": [
            { data: 'member.first_name', name: 'member.first_name' },
            { data: 'savings_account.account_number', name: 'savings_account.account_number' },
            { data: 'total_contributed', name: 'total_contributed' },
            { data: 'payout_position_number', name: 'payout_position_number' },
            { data: 'has_received_payout', name: 'has_received_payout' },
            { data: 'amount_received', name: 'amount_received' },
            { data: 'has_received_payout_date', name: 'has_received_payout_date' },
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
        group_members_table.draw();
    });

   
   
    // Reset and Shuffle Button
$(document).on('click', '#reset-button', function () {
    var groupId = $(this).data('group-id');

    Swal.fire({
        title: "{{ _lang('Are you sure?') }}",
        text: "{{ _lang('You will reset all the payout statuses and shuffle the members.') }}",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "{{ _lang('Yes, Reset') }}",
        cancelButtonText: "{{ _lang('No, Cancel') }}"
    }).then((result) => {
        console.log("SweetAlert result:", result); // Debugging

        if (result.value) {  // Use result.value instead of result.isConfirmed
            console.log("User confirmed. Proceeding with AJAX request...");

            $.ajax({
                url: "{{ route('group_members.resetContributionAndPayoutPosition', ':group_id') }}".replace(':group_id', groupId),
                type: "POST",
                data: { _token: "{{ csrf_token() }}" },
                success: function (response) {
                    console.log("AJAX success response:", response);
                    Swal.fire('Success', response.message, 'success');
                    $('#group_members_table').DataTable().ajax.reload(null, false);
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", xhr.responseText);
                    Swal.fire('Error', 'An error occurred while resetting.', 'error');
                }
            });
        } else {
            console.log("User canceled the reset action.");
        }
    }).catch((error) => {
        console.error("SweetAlert error:", error);
    });
});



})(jQuery);

//Update Payout Status
function updatePayoutStatus(memberId) {
    $.ajax({
        url: '{{ route("group_members.updatePayoutStatus", ":id") }}'.replace(':id', memberId),  // Use route() to generate the correct URL
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            // Show SweetAlert success message
            Swal.fire({
                title: "Success!",
                text: response.message,
                icon: "success",
                timer: 2000, // Auto-close after 2 seconds
                showConfirmButton: false
            });

            // Update the status text in the table
            $('#group_members_table').DataTable().ajax.reload(null, false);
        },
        error: function(xhr, status, error) {
            console.error(error);
            Swal.fire({
                title: "Error!",
                text: "An error occurred while updating the payout status.",
                icon: "error"
            });
        }
    });
}

</script>
@endsection
