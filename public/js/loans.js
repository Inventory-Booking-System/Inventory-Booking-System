var equipmentCart = {};
var equipmentTable;

$("document").ready(function(){

    //Fix for missing icons in tempusdominus
    $.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, { icons: { time: 'fas fa-clock', date: 'fas fa-calendar', up: 'fas fa-arrow-up', down: 'fas fa-arrow-down', previous: 'far fa-chevron-left', next: 'far fa-chevron-right', today: 'far fa-calendar-check-o', clear: 'far fa-trash', close: 'far fa-times' } });

    //Setup better select boxes
    $('#userSelected').select2({
        theme: "bootstrap-5",
        placeholder: "Select a user",
    });
    $('#equipmentSelected').select2({
        theme: "bootstrap-5",
        placeholder: "Select equipment",
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
        },
        "columns": [
            { "width": "80%" },
            { "width": "20%" },
            null,
        ]
    });

    //Get any equipment already in the equipment table. For example if formed was filled out incorrectly
    //we need to repopulate any equipment returned back into the shopping cart
    if(equipmentTable.data().any()){
        equipmentTable.rows().every(function(index, element){
            var assetID = this.node().id;
            var assetName = this.data()[0];

            //Add to the shopping card to pass onto the database for storage
            equipmentCart[assetID] = {}
            equipmentCart[assetID]['returned'] = 0

            //Set the equipment array to a hidden input on the form
            //Must be sent in json format and not a javascript array
            $('#equipmentToSubmit').val(JSON.stringify(equipmentCart));
        });

        console.log("HERE");
        console.log(equipmentCart);

        //Populate dropdown
        getEquipment();
    }else{
        //Check if we have start and loan date filled out so we can fetch all equipment
        allInputsHaveData = checkInputFieldsForData(["loanStartDate","loanEndDate"]);

        if(allInputsHaveData){
            getEquipment();
        }
    }

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

    //Return a list of equipment that is avaliable for booking
    function getEquipment(){
        //On Modify Booking we need to fetch the id from the url
        var url = window.location.href;
        var id = null

        if(url.includes('edit')){
          console.log("HERE");
          id = url.split("/")[4];
          console.log(id);
        }

        jQuery.ajax({
            type: "GET",
            url: "/loans/getBookableEquipment",
            async: false,
            dataType: 'json',
            data: {
                loanType: $("#formAddLoan input[type='radio']:checked").attr('id'),
                start_date: $('#loanStartDate').val(),
                end_date: $('#loanEndDate').val(),
                equipmentSelected: equipmentCart,
                id, id
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
        dropdown.append("<option></option>");

        //Populate dropdown options
        console.log(data);
        $.each(data, function() {
            //Make sure item is not already in the shopping cart
            if(!(this.id in equipmentCart)){
                $("<option />", {
                    val: this.id,
                    text: this.name + " (" + this.tag + ")"
                }).appendTo(dropdown);
            }
        });

        //Check if any items are in the shopping cart that shouldn't be
        //This can happen when the user changes the start/end date and
        //asset is no longer avalaible to book.
        if(equipmentTable.data().any()){
            equipmentTable.rows().every(function(index, element){
                var assetID = this.node().id;
                var assetName = this.data()[0];

                var found = false;
                $.each(data, function() {
                    if(this.id == assetID){
                        found = true;
                    }
                });

                if(found == false){
                    console.log(assetName + " needs removing");
                    delete equipmentCart[assetID];

                    $('#equipmentToSubmit').val(JSON.stringify(equipmentCart));

                    //Remove from table
                    document.getElementById(assetID).classList.add('addStrike');
                }else{
                    document.getElementById(assetID).classList.remove('addStrike');
                }
            });
        }

        //If you select the equipment too quickly it returns blank so disable for one second
        //TODO: figure out why this is?
        $("#equipmentSelected").attr('disabled',true);
        setTimeout(function() {
            $("#equipmentSelected").attr('disabled',false);
        }, 1000);
    }

    //Add selected equipment to the shopping cart
    $(document).on('change','#equipmentSelected',function (e) {
        console.log(e);

        //Find what has just been selected
        var assetName = $(e.target).find("option:selected").text();
        var assetID = $(e.target).val();

        //Fill out datatable on form
        //Must redraw after adding to show user the changes
        equipmentTable.row.add( [
            assetName,
            '<button class="removeFromCart btn btn-danger btn-sm rounded-0" type="button" data-assetname="' + assetName + '" data-assetid="' + $(this).val() + '" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-can"></i></button>'
        ] ).node().id = assetID;
        equipmentTable.draw();

        //Add to the shopping card to pass onto the database for storage
        equipmentCart[assetID] = {}
        equipmentCart[assetID]['returned'] = 0

        //Set the equipment array to a hidden input on the form
        //Must be sent in json format and not a javascript array
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
        delete equipmentCart[this.dataset.assetid];

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
        equipmentCart[this.dataset.assetid] = {}
        equipmentCart[this.dataset.assetid]['returned'] = 1

        console.log(equipmentCart);

        //Remove from table
        equipmentTable.row($(this).parents('tr')).remove().draw();
    });
});