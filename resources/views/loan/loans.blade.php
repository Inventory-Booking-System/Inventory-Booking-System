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
        var equipmentCart = [];
        var equipmentTable;

        $("document").ready(function(){

        //Fix for missing icons in tempusdominus
        $.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, { icons: { time: 'fas fa-clock', date: 'fas fa-calendar', up: 'fas fa-arrow-up', down: 'fas fa-arrow-down', previous: 'far fa-chevron-left', next: 'far fa-chevron-right', today: 'far fa-calendar-check-o', clear: 'far fa-trash', close: 'far fa-times' } });

        //Populate loans table on page load using Datables plugin
        loanTable = $('#loansTable').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "loans",
            columns: [
                {data: 'id',name: 'id'},
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

        //Check that each input is filled out with data
        function checkInputFieldsForData(inputs, type){
            var dataMissing = false;
            inputs.forEach((input) => {
                if(!($("#" + input, '.bootbox').val())){
                    console.log(input + " missing");
                    dataMissing = true;
                    return false;
                }
            });

            if(dataMissing){
                return false;
            }else{
                return true;
            }
        }

        //Return a list of equipement that is avaliable for booking
        function getEquipment(){
            jQuery.ajax({
                type: "GET",
                url: "loans/getBookableEquipment",
                async: false,
                dataType: 'json',
                data: {
                    user_id: $('#userSelected :selected','.bootbox').val(),
                    status_id: $('#reservation','.bootbox').is(':checked') ? 1 : 0,
                    loanType: $("#formAddLoan input[type='radio']:checked", '.bootbox').attr('id'),
                    start_date: $('#loanStartDate','.bootbox').val(),
                    end_date: $('#loanEndDate','.bootbox').val(),
                    equipmentSelected: equipmentCart,
                    details: $('#additionalDetails','.bootbox').val(),
                },
                success: function(data) {
                    populateEquipmentDropdown("equipmentSelected", data);
                },
                error: function(data){

                }
            });
        }

        //List all the avaliable equipment for booking on the bootbox form
        function populateEquipmentDropdown(name, data){
            var dropdown = $('#' + name, '.bootbox');
            $(dropdown).empty();
            dropdown.append("<option selected>Choose here</option>");
            $.each(data, function() {
                $("<option />", {
                    val: this.id,
                    text: this.name
                }).appendTo(dropdown);
            });
        }

        //Add selected equipment to the shopping cart
        $(document).on('change','#equipmentSelected',function (e) {
            //Add to datatable
            var assetName = $('#equipmentSelected :selected', '.bootbox').text();
            var assetID = $('#equipmentSelected','.bootbox').children(":selected").val();
            equipmentTable.row.add( [
                assetName,
                '<button class="removeFromCart btn btn-danger btn-sm rounded-0" type="button" data-assetname="' + assetName + '" data-assetid="' + $(this).val() + '" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-can"></i></button>'
            ] ).node().id = assetID;
            equipmentTable.draw();

            //Add to shopping cart array
            equipmentCart.push({
                asset_id: assetID,
                returned: 0,
            });

            //Remove from the dropdown menu
            $('#equipmentSelected :selected', '.bootbox').remove();
        });

        //Delete item in shopping cart
        $(document).on('click', '.removeFromCart', function(e) {
            var dropdown = $('#equipmentSelected', '.bootbox');
            //Re-add to equipment dropdown
            $("<option />", {
                val: this.dataset.assetid,
                text: this.dataset.assetname
            }).appendTo(dropdown);

            //Remove from shopping cart array
            var index = equipmentCart.findIndex((obj => obj.asset_id == this.dataset.assetid));
            if (index > -1) {
                equipmentCart.splice(index, 1);
            }

            //Remove from table
            equipmentTable.row($(this).parents('tr')).remove().draw();
        });

        //Book in individual item from the shopping cart
        $(document).on('click', '.bookFromCart', function(e) {
            //Send ajax request to update database and send email

            var dropdown = $('#equipmentSelected', '.bootbox');
            //Re-add to equipment dropdown
            $("<option />", {
                val: this.dataset.assetid,
                text: this.dataset.assetname
            }).appendTo(dropdown);

            console.log(equipmentCart);

            //Marked as returned in the shopping cart
            var objIndex = equipmentCart.findIndex((obj => obj.asset_id == this.dataset.assetid));
            console.log(objIndex)

            equipmentCart[objIndex].returned = 1;

            //Remove from table
            equipmentTable.row($(this).parents('tr')).remove().draw();
        });
    });
    </script>
@endpush

