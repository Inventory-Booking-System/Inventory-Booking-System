@extends('layouts.app')

@section('navItems')
    <li class="nav-item">
        <a id="addLoan" href="#" class="nav-link">
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
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Details</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@include('loan.modals.add')
@include('loan.modals.delete')
@include('loan.modals.modify')

@push('scripts')
    {{-- <script src="{{ asset('js/loans.js') }}"></script> --}}

    <script>
        var equipmentCart = [];
        var equipmentTable;

        $("document").ready(function(){

        //Populate loans table on page load using Datables plugin
        loanTable = $('#loansTable').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "loans",
            columns: [
                {data: 'id',name: 'id'},
                {data: 'start_date', name: 'start_date'},
                {data: 'end_date', name: 'end_date'},
                {data: 'start_time', name: 'start_time'},
                {data: 'end_time', name: 'end_time'},
                {data: 'details', name: 'details'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        //Add new loan to database
        $('#addLoan').on('click', function (e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            var modal = bootbox.dialog({
                message: $(".addLoan").html(),
                size: "large",
                title: "Create New Loan",
                buttons: [
                {
                    label: "Save",
                    className: "btn btn-primary pull-right",
                    callback: function(result) {
                        //Send ajax request to the server to save to database and then update the table on the website accordingly
                        jQuery.ajax({
                            type: "POST",
                            url: "loans",
                            async: false,
                            dataType: 'json',
                            data: {
                                loanType: $("#formAddLoan input[type='radio']:checked", '.bootbox').val(),
                                loanDate: $('#loanDate', '.bootbox').val(),
                                loanStartTime: $('#loanStartTime', '.bootbox').val(),
                                loanEndTime: $('#loanEndTime','.bootbox').val(),
                                loanStartDate: $('#loanStartDate','.bootbox').val(),
                                loanEndDate: $('#loanEndDate','.bootbox').val(),
                                equipment: equipmentCart,
                                user: $('#userSelected :selected','.bootbox').text(),
                                additionalDetails: $('#additionalDetails','.bootbox').val(),
                                reservation: $('#reservation','.bootbox').is(':checked') ? 1 : 0
                            },
                            success: function(data) {
                                //Popup to tell the user the action has completed successfully
                                toastr.success(data['name'] + ' has been created');

                                //Re-populate the table
                                loanTable.ajax.reload();

                                //Close the model
                                modal.modal("hide");
                            },
                            error: function(data){
                                //Clear all errors currently being displayed
                                $('.inputError').each(function(i, obj) {
                                    $(this).html("");
                                });

                                $.each(data['responseJSON']['errors'], function(key, data){
                                    OutputDataEntryError(key, data);
                                })
                            }
                        });

                        return false;
                    }
                },
                {
                    label: "Cancel",
                    className: "btn btn-danger pull-right",
                }
                ],
                onEscape: function() {
                    modal.modal("hide");
                }
            });

            //Modal is displaying. Lets setup various input boxes and datetimepickers

            //Hide the date & time selection options from the user until they select
            //the type of booking that is being made
            $("#singleDayBooking",'.bootbox').hide();
            $("#multiDayBooking",'.bootbox').hide();

            //Setup the Datetime picker settings
            var currentDate = new Date();

            //Single Day Booking
            //Loan Date
            $('.datetimepicker6').datetimepicker({
                format: "YYYY-MM-DD",
                minDate: currentDate
            });
            $(document).on('input', '#loanDate, #loanStartTime, #loanEndTime, #loanStartDate, #loanEndDate', function(e) {
                var allInputsHaveData = false;
                if($("#loanTypeSingle",'.bootbox').is(':checked')){
                    allInputsHaveData = checkInputFieldsForData(['loanDate',"loanStartTime","loanEndTime"]);
                }else if($("#loanTypeMulti",'.bootbox').is(':checked')){
                    allInputsHaveData = checkInputFieldsForData(["loanStartDate","loanEndDate"]);
                }

                if(allInputsHaveData){
                    getEquipment();
                }
            });

            //Start Time
            $('.datetimepicker9').datetimepicker({
                useCurrent: true,
                format: "HH:mm",
            });
            $(".datetimepicker9").on("show.datetimepicker", function (e) {
                $('.datetimepicker10').datetimepicker('minDate', e.date);
            });

            //End Time
            $('.datetimepicker10').datetimepicker({
                useCurrent: true,
                format: "HH:mm",
                minDate: $('.datetimepicker9').datetimepicker('minDate', e.date),
            });
            $(".datetimepicker10").on("show.datetimepicker", function (e) {
                $('.datetimepicker9').datetimepicker('maxDate', e.date);
            });

            //Multi-Day Booking
            //Start Date
            $('.datetimepicker7').datetimepicker({
                format: "YYYY-MM-DD",
                minDate: currentDate
            });
            $(".datetimepicker7").on("show.datetimepicker", function (e) {
                $('.datetimepicker8').datetimepicker('minDate', e.date);
            });

            //End Date
            $('.datetimepicker8').datetimepicker({
                useCurrent: false,
                format: "YYYY-MM-DD",
                minDate: currentDate
            });
            $(".datetimepicker8").on("show.datetimepicker", function (e) {
                $('.datetimepicker7').datetimepicker('maxDate', e.date);
            });

            //This table is used to display the list of equipment currently in the shopping cart
            equipmentTable = $('#equipmentTable', '.bootbox').DataTable({
                paging: false,
                searching: false,
                info: false,
                language: {
                emptyTable: "Shopping cart is empty"
                }
            });
        });

        //Delete asset from database
        $("#assetTable").on('click', '.deleteAsset', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            //Get the id of the asset we are deleting
            var id = $(this).closest("tr").attr("id");

            var modal = bootbox.dialog({
                message: $(".deleteAsset").html(),
                size: "large",
                title: "Delete Asset",
                buttons: [
                {
                    label: "Delete",
                    className: "btn btn-danger pull-right",
                    callback: function(result) {
                        //Send ajax request to the server to save to database and then update the table on the website accordingly
                        jQuery.ajax({
                            type: "DELETE",
                            url: "assets/"+id,
                            dataType: 'json',
                            success: function(data) {
                                //Popup to tell the user the action has completed successfully
                                toastr.success(data['name'] + ' has been deleted');

                                //Re-populate the table
                                assetTable.ajax.reload();
                            },
                            error: function(data){
                                toastr.error('Asset could not be deleted');
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
        $("#assetTable").on('click', '.modifyAsset', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            //Get the id of the asset we are deleting
            var id = $(this).closest("tr").attr("id");

            //Get data to populate the model
            jQuery.ajax({
                type: "GET",
                url: "assets/"+id,
                dataType: 'json',
                success: function(data) {
                    $('#assetName', '.bootbox').val(data.name),
                    $('#assetDescription', '.bootbox').val(data.description),
                    $('#assetTag', '.bootbox').val(data.tag),
                    $('#assetCost','.bootbox').val(data.cost),
                    $('#assetBookable','.bootbox').prop('checked', data.bookable ? true : false)
                },
                error: function(data){
                }
            });

            //Show model and handle saving data back to database
            var modal = bootbox.dialog({
                message: $(".addAsset").html(),
                size: "large",
                title: "Modify Asset",
                buttons: [
                {
                    label: "Save",
                    className: "btn btn-primary pull-right",
                    callback: function(result) {
                        //Send ajax request to the server to save to database and then update the table on the website accordingly
                        jQuery.ajax({
                            type: "PATCH",
                            url: "assets/"+id,
                            dataType: 'json',
                            async: false,
                            data: {
                                name: $('#assetName', '.bootbox').val(),
                                description: $('#assetDescription', '.bootbox').val(),
                                tag: $('#assetTag', '.bootbox').val(),
                                cost: $('#assetCost','.bootbox').val(),
                                bookable: $('#assetBookable','.bootbox').is(':checked') ? 1 : 0
                            },
                            success: function(data) {
                                //Popup to tell the user the action has completed successfully
                                toastr.success(data['name'] + ' has been modified');

                                //Re-populate the table
                                assetTable.ajax.reload();

                                //Close the model
                                modal.modal("hide");
                            },
                            error: function(data){
                                //Clear all errors currently being displayed
                                $('.inputError').each(function(i, obj) {
                                    $(this).html("");
                                });

                                $.each(data['responseJSON']['errors'], function(key, data){
                                    OutputDataEntryError(key, data);
                                })
                            }
                        });

                        return false;
                    }
                },
                {
                    label: "Cancel",
                    className: "btn btn-danger pull-right",
                }
                ],
                onEscape: function() {
                    modal.modal("hide");
                }
            });
        });

        //Show Multi-Day Booking
        $(document).on('change', '#loanTypeMulti', function() {
            console.log("Multi day booking selected");
            //$('#equipmentTable tbody > tr').remove();
            //$('#equipmentSelected option').remove();



            if($(this).is(':checked')){
                $("#singleDayBooking",'.bootbox').hide();
                $("#multiDayBooking",'.bootbox').show();
                //loanType = "multi";
            }
        });

        //Show Single Day Booking
        $(document).on('change', '#loanTypeSingle', function() {
            console.log("Single day booking selected");


            //$('#equipmentTable tbody > tr').remove();
            //$('#equipmentSelected option').remove();
            if($(this).is(':checked')){
                $("#singleDayBooking",'.bootbox').show();
                $("#multiDayBooking",'.bootbox').hide();
                //loanType = "single";
            }
        });

        //Check that each input is filled out with data
        function checkInputFieldsForData(inputs, type){
            var dataMissing = false;
            inputs.forEach((input) => {
                if(!($("#" + input).val())){
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
                    loanTypeSingle: $('#loanTypeSingle', '.bootbox').val(),
                    loanTypeMulti: $('#loanTypeMulti', '.bootbox').val(),
                    loanDate: $('#loanDate', '.bootbox').val(),
                    loanStartTime: $('#loanStartTime', '.bootbox').val(),
                    loanEndTime: $('#loanEndTime', '.bootbox').val(),
                    loanStartDate: $('#loanStartDate','.bootbox').val(),
                    loanEndDate: $('#loanEndDate','.bootbox').val(),
                    equipmentCart: equipmentCart
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
            dropdown.append("<option selected disabled>Choose here</option>");
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
            equipmentCart.push(assetID);

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
            const index = equipmentCart.indexOf($(this).attr('data-assetid'));
            if (index > -1) {
                equipmentCart.splice(index, 1);
            }

            //Remove from table
            equipmentTable.row($(this).parents('tr')).remove().draw();
        });
    });
    </script>
@endpush

