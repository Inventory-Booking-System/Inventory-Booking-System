@extends('layouts.app')

@section('mainContent')
    <form action="/loans" method="POST" enctype="multipart/form-data" >
        @csrf

        <!-- Loan Start Date -->
        <label id="loanStartDateLabel">Start Date</label>
        @if($errors->has('start_date'))
            <p class="text-danger">{{ $errors->first('start_date') }}</p>
        @endif
        <div class="input-group date dtpStartDateTime" data-target-input="nearest">
            <input name="start_date" id="loanStartDate" type="text" value="{{ old('start_date') }}" class="form-control datetimepicker-input" data-target=".dtpStartDateTime"/>
            <div class="input-group-append" data-target=".dtpStartDateTime" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
        </div>

        <!-- Loan End Date -->
        <label id="loanEndDateLabel">End Date</label>
        @if($errors->has('end_date'))
            <p class="text-danger">{{ $errors->first('end_date') }}</p>
        @endif
        <div class="input-group date dtpEndDateTime" data-target-input="nearest">
            <input name="end_date" id="loanEndDate" type="text" value="{{ old('end_date') }}" class="form-control datetimepicker-input" data-target=".dtpEndDateTime"/>
            <div class="input-group-append" data-target=".dtpEndDateTime" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
            </div>
        </div>

        <!-- User Selected -->
        <label id="userSelectedLabel">User</label>
        @if($errors->has('user_id'))
            <p class="text-danger">{{ $errors->first('user_id') }}</p>
        @endif
        <select name="user_id" class="form-control" id="userSelected" value="{{ old('user_id') }}">
            <option>Please select a user...</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->forename }} {{ $user->surname }}</option>
            @endforeach
        </select>

        <!-- Equipment -->
        <label id="equipmentTableLabel">Equipment</label>
        @if($errors->has('equipmentSelected'))
            <p class="text-danger">{{ $errors->first('equipmentSelected') }}</p>
        @endif
        <select class="form-control" id="equipmentSelected">
        </select>

        <div id="equipmentList">
            <table class="table" id="equipmentTable">
                <thead>
                    <tr>
                        <th scope="col">Item</th>
                        <th scope="col">Remove</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <input type="hidden" id="equipmentToSubmit" name="equipmentSelected">

        <!-- Additional Details -->
        <label>Additional details</label>
        <span id="additionalDetailsError" class="inputError"></span>
        <textarea name="details" class="form-control" id="additionalDetails" value="{{ old('details') }}"></textarea>

        <!-- Reservation -->
        <hr>
        <div class="form-check">
        <input class="form-check-input" type="checkbox" name="reservation" id="reservation" value="{{ old('reservation') }}">
        <span id="status_idError" class="inputError"></span>
        <label class="form-check-label" for="defaultCheck1">
            Reservation
        </label>
        </div>

        <button type="submit" class="btn btn-primary">Create</button>
    </form>
@endsection

@push('scripts')
    {{-- <script src="{{ asset('js/loans.js') }}"></script> --}}

    <script>
        var equipmentCart = [];
        var equipmentTable;

        $("document").ready(function(){

            //Fix for missing icons in tempusdominus
            $.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, { icons: { time: 'fas fa-clock', date: 'fas fa-calendar', up: 'fas fa-arrow-up', down: 'fas fa-arrow-down', previous: 'far fa-chevron-left', next: 'far fa-chevron-right', today: 'far fa-calendar-check-o', clear: 'far fa-trash', close: 'far fa-times' } });

            $('#userSelected').select2({
                theme: "bootstrap-5",
            });

            //Setup the Datetime picker settings
            var currentDate = new Date();

            //Single Day Booking
            //Loan Date
            $('.dtpStartDateTime').datetimepicker({
                useCurrent: true,
                format: "yyyy-MM-DD HH:mm",
            });

            $('.dtpEndDateTime').datetimepicker({
                format: "yyyy-MM-DD HH:mm",
            });

            $(document).on('input', '#loanStartDate, #loanEndDate', function(e) {
                var allInputsHaveData = false;
                allInputsHaveData = checkInputFieldsForData(["loanStartDate","loanEndDate"]);

                if(allInputsHaveData){
                    getEquipment();
                }
            });

            //This table is used to display the list of equipment currently in the shopping cart
            equipmentCart.length = 0;
            equipmentTable = $('#equipmentTable').DataTable({
                paging: false,
                searching: false,
                info: false,
                language: {
                emptyTable: "Shopping cart is empty"
                }
            });

            //Check that each input is filled out with data
            function checkInputFieldsForData(inputs, type){
                var dataMissing = false;
                inputs.forEach((input) => {
                    if(!($("#" + input).val())){
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
                    url: "getBookableEquipment",
                    async: false,
                    dataType: 'json',
                    data: {
                        user_id: $('#userSelected :selected').val(),
                        status_id: $('#reservation').is(':checked') ? 1 : 0,
                        loanType: $("#formAddLoan input[type='radio']:checked").attr('id'),
                        start_date: $('#loanStartDate').val(),
                        end_date: $('#loanEndDate').val(),
                        equipmentSelected: equipmentCart,
                        details: $('#additionalDetails').val(),
                    },
                    success: function(data) {
                        populateEquipmentDropdown("equipmentSelected", data);
                    },
                    error: function(data){

                    }
                });
            }

            //List all the avaliable equipment for booking on the form
            function populateEquipmentDropdown(name, data){
                var dropdown = $('#' + name);
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
                var assetName = $('#equipmentSelected :selected').text();
                var assetID = $('#equipmentSelected').children(":selected").val();
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
                $('#equipmentToSubmit').val(JSON.stringify(equipmentCart));

                //Remove from the dropdown menu
                $('#equipmentSelected :selected').remove();
            });

            //Delete item in shopping cart
            $(document).on('click', '.removeFromCart', function(e) {
                var dropdown = $('#equipmentSelected');
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
                $('#equipmentToSubmit').val(JSON.stringify(equipmentCart));

                //Remove from table
                equipmentTable.row($(this).parents('tr')).remove().draw();
            });

            //Book in individual item from the shopping cart
            $(document).on('click', '.bookFromCart', function(e) {
                //Send ajax request to update database and send email

                var dropdown = $('#equipmentSelected');
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