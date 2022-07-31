@extends('layouts.app')

@section('navItems')
    <li class="nav-item">
        <a id="addLoan" href="/loans/create" class="nav-link">
        <i class="nav-icon fas fa-plus"></i>
        <p>
            New Loan
        </p>
        </a>
    </li>
@endsection

@section('mainContent')
    <div id="assetTable" class="card-body">
        <table id='loansTable' class='table yajra-datatable' width="100%">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Details</th>
                    <th>Assets</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@include('loan.modals.delete')
@include('loan.modals.modify')

@push('scripts')
    {{-- <script src="{{ asset('js/loans.js') }}"></script> --}}

    <script>
        $("document").ready(function(){

        //Fix for missing icons in tempusdominus
        $.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, { icons: { time: 'fas fa-clock', date: 'fas fa-calendar', up: 'fas fa-arrow-up', down: 'fas fa-arrow-down', previous: 'far fa-chevron-left', next: 'far fa-chevron-right', today: 'far fa-calendar-check-o', clear: 'far fa-trash', close: 'far fa-times' } });

        //Populate loans table on page load using Datables plugin
        loanTable = $('#loansTable').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "loans",
            "pageLength": 25,
            columns: [
                {data: 'id',name: 'id'},
                {
                    data: function (row) {
                        return row.user.forename + " " + row.user.surname;
                    },
                    name: 'users.name'
                },
                {data: 'status_id',name: 'status_id'},
                {data: 'start_date_time', name: 'start_date_time'},
                {data: 'end_date_time', name: 'end_date_time'},
                {data: 'details', name: 'details'},
                {
                    data: function (row) {
                        console.log(row);
                        let assetsNames= [];
                        $(row.assets).each(function (index, asset) {
                            if(asset.pivot['returned'] == 1){
                                assetsNames.push("<del>" + asset.name + " (" + asset.tag + ")</del>");
                            }else{
                                assetsNames.push(asset.name + " (" + asset.tag + ")");
                            }
                        });
                        return assetsNames.join("<br>")
                    },
                    name: 'assets.name'
                },
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        //Complete Loan
        $("#loansTable").on('click', '.completeLoan', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            //Get the id of the asset we are deleting
            var id = $(this).closest("tr").attr("id");

            var modal = bootbox.dialog({
                message: $(".deleteLoan").html(),
                size: "large",
                title: "Complete Loan",
                buttons: [
                {
                    label: "Complete",
                    className: "btn btn-danger pull-right",
                    callback: function(result) {
                        //Send ajax request to the server to save to database and then update the table on the website accordingly
                        jQuery.ajax({
                            type: "PATCH",
                            url: "loans/completeBooking/"+id,
                            dataType: 'json',
                            success: function(data) {
                                //Popup to tell the user the action has completed successfully
                                toastr.success('Booking #' + data['id'] + ' has been completed');

                                //Re-populate the table
                                loanTable.ajax.reload();
                            },
                            error: function(data){
                                toastr.error('Loan could not be completed');
                                console.log(data);
                            }
                        });
                    }
                },
                {
                    label: "Cancel",
                    className: "btn btn-success pull-right",
                }
                ],
                onEscape: function() {
                    modal.modal("hide");
                }
            });
        });

        //Book Out asset from database
        $("#loansTable").on('click', '.bookOutLoan', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            //Get the id of the asset we are deleting
            var id = $(this).closest("tr").attr("id");

            var modal = bootbox.dialog({
                message: $(".bookOutLoan ").html(),
                size: "large",
                title: "Book Out Loan",
                buttons: [
                {
                    label: "Book Out",
                    className: "btn btn-danger pull-right",
                    callback: function(result) {
                        //Send ajax request to the server to save to database and then update the table on the website accordingly
                        jQuery.ajax({
                            type: "PATCH",
                            url: "loans/bookOutBooking/"+id,
                            dataType: 'json',
                            success: function(data) {
                                //Popup to tell the user the action has completed successfully
                                toastr.success('Booking #' + data['id'] + ' has been booked out');

                                //Re-populate the table
                                loanTable.ajax.reload();
                            },
                            error: function(data){
                                toastr.error('Loan could not be booked out');
                            }
                        });
                    }
                },
                {
                    label: "Cancel",
                    className: "btn btn-success pull-right",
                }
                ],
                onEscape: function() {
                    modal.modal("hide");
                }
            });
        });

        //Delete asset from database
        $("#loansTable").on('click', '.deleteLoan', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            //Get the id of the asset we are deleting
            var id = $(this).closest("tr").attr("id");

            var modal = bootbox.dialog({
                message: $(".deleteLoan").html(),
                size: "large",
                title: "Delete Loan",
                buttons: [
                {
                    label: "Delete",
                    className: "btn btn-danger pull-right",
                    callback: function(result) {
                        //Send ajax request to the server to save to database and then update the table on the website accordingly
                        jQuery.ajax({
                            type: "DELETE",
                            url: "loans/"+id,
                            dataType: 'json',
                            success: function(data) {
                                //Popup to tell the user the action has completed successfully
                                toastr.success('Booking #' + data['id'] + ' has been deleted');

                                //Re-populate the table
                                loanTable.ajax.reload();
                            },
                            error: function(data){
                                toastr.error('Loan could not be deleted');
                            }
                        });
                    }
                },
                {
                    label: "Cancel",
                    className: "btn btn-success pull-right",
                }
                ],
                onEscape: function() {
                    modal.modal("hide");
                }
            });
        });

        //Modify asset in database
        $("#loansTable").on('click', '.modifyLoan', function() {
            //Get the id of the asset we are modifying and redirect
            var id = $(this).closest("tr").attr("id");
            window.location = "/loans/" + id + "/edit"
        });
    });
    </script>
@endpush

