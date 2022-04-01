@extends('layouts.app')

@section('navItems')
    <li class="nav-item">
        <a id="addAsset" href="#" class="nav-link">
        <i class="nav-icon fas fa-plus"></i>
        <p>
            Add Asset
        </p>
        </a>
    </li>
@endsection

@section('mainContent')
    <div id="assetTable" class="card-body">
        <table id='assetsTable' class='table yajra-datatable' width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Asset Tag</th>
                    <th>Description</th>
                    <th>Cost</th>
                    <th>Bookable</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@include('asset.modals.add')
@include('asset.modals.delete')

@section('scripts')
<script type="text/javascript">
    //Populate Asset table on page load using Datables plugin
    var assetTable;
    $("document").ready(function(){
        assetTable = $('#assetsTable').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "assets",
            columns: [
                {
                    data: 'name',
                    name: 'name',
                    // render: function(data, type, full, meta){
                    //     return "<a href='#'>" + data + "</a>";
                    // }
                },
                {data: 'tag', name: 'tag'},
                {data: 'description', name: 'description'},
                {data: 'cost', name: 'cost'},
                {data: 'bookable', name: 'bookable'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    });

    //Add new asset to database
    $('#addAsset').on('click', function (e) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var modal = bootbox.dialog({
            message: $(".addAsset").html(),
            size: "large",
            title: "Add New Asset",
            buttons: [
            {
                label: "Save",
                className: "btn btn-primary pull-right",
                callback: function(result) {
                    var validationError = true;

                    //Send ajax request to the server to save to database and then update the table on the website accordingly
                    jQuery.ajax({
                        type: "POST",
                        url: "assets",
                        async: false,
                        dataType: 'json',
                        data: {
                            name: $('#assetName', '.bootbox').val(),
                            description: $('#assetDescription', '.bootbox').val(),
                            tag: $('#assetTag', '.bootbox').val(),
                            cost: $('#assetCost','.bootbox').val(),
                            bookable: $('#assetBookable','.bootbox').is(':checked') ? 1 : 0
                        },
                        success: function(data) {
                            //Allows the form to close
                            validationError = false;

                            //Popup to tell the user the action has completed successfully
                            toastr.success(data['name'] + ' has been created');

                            //Re-populate the table
                            assetTable.ajax.reload();
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

                    if(validationError == true){
                         return false;
                    }
                }
            },
            {
                label: "Cancel",
                className: "btn btn-danger pull-right",
            }
            ],
            show: false,
            onEscape: function() {
                modal.modal("hide");
            }
        });
        modal.modal("show");
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
                            //Allows the form to close
                            validationError = false;

                            //Popup to tell the user the action has completed successfully
                            toastr.success(data['name'] + ' has been deleted');

                            //Re-populate the table
                            assetTable.ajax.reload();
                        },
                        error: function(data){
                            toastr.error(data['name'] + ' could not be deleted');
                        }
                    }); 
                }
            },
            {
                label: "Cancel",
                className: "btn btn-success pull-right",
            }
            ],
            show: false,
            onEscape: function() {
                modal.modal("hide");
            }
        });
        modal.modal("show");
    });

    //Modify asset in database
    $('#modifyAsset').on('click', function (e) {
        var modal = bootbox.dialog({
            message: $(".modifyAsset").html(),
            size: "large",
            title: "Modify Asset",
            buttons: [
            {
                label: "Save",
                className: "btn btn-primary pull-right",
                callback: function(result) {
                    //Get the data that was input into each field
                    var assetName = $('#assetNewName', '.bootbox').val();
                    var assetDescription = $('#assetNewDescription', '.bootbox').val();
                    var assetTag = $('#assetNewTag', '.bootbox').val();
                    var assetLocation = $('#assetNewLocation','.bootbox').val();
                    var assetID = $('#assetNewID','.bootbox').val();
                    var originalAssetTag = $('#originalAssetTag', '.bootbox').val();
                    var validationError = true;

                    //Send ajax request to the server to save to database and then update the table on the website accordingly
                    jQuery.ajax({
                        type: "POST",
                        url: "index.php/manageassets/updateAsset",
                        data: {originalAssetTag: originalAssetTag, assetID: assetID, assetName: assetName, assetDescription: assetDescription, assetTag: assetTag, assetLocation: assetLocation},
                        success: function(message) {
                            //If message returned is "success" then insert new asset into table and add to other dropdowns
                            console.log(message);
                            if(message == "Success"){
                                validationError = false;
                                toastr.success(assetName + ' has been modified');

                                //Modify Asset in Delete Asset
                                $("#Delete-" + assetID).remove();
                                $("#Modify-" + assetID).remove();
                                $("<option id='Modify-" + assetID + "'>" + assetName + " (" + assetTag + ")</option>").appendTo("#assetToModify");
                                $("<option id='Delete-" + assetID + "'>" + assetName + " (" + assetTag + ")</option>").appendTo("#assetToDelete");

                                //Update Table
                                jQuery.ajax({
                                    type: "POST",
                                    url: "index.php/manageassets/getListOfAssets",
                                    success: function(message){
                                        $("#assetsTable").html(message);
                                        modal.modal("hide");
                                    }
                                });
                            }else{
                                $('#errorTextModify','.bootbox').html(message + "<br>");
                            }
                        }
                    });

                    if(validationError == true){
                        return false;
                    }
                }
            },
            {
                label: "Cancel",
                className: "btn btn-danger pull-right",
            }
            ],
            show: false,
            onEscape: function() {
                modal.modal("hide");
            }
        });
        modal.modal("show");
    });

    //Modify asset "Select Asset To Modify" clicked so load in relevant information
    $(document).on('click', '#assetToModify', function(e){
        //Clear fields in case the ajax query fails
        $("#assetNewName", '.bootbox').val("");
        $("#assetNewDescription", '.bootbox').val("");
        $("#assetNewTag", '.bootbox').val("");
        $("#assetNewLocation", '.bootbox').val("");
        $("#assetNewID", '.bootbox').val("");
        $("#originalAssetTag", '.bootbox').val("");

        var assetID = $(".modal-body #assetToModify").children(":selected").attr("id").split("-")[1];

        //Fetch information about this assetTag from the database and return as JSON
        jQuery.ajax({
            type: "POST",
            url: "index.php/manageassets/getAsset",
            data: {assetID: assetID},
            success: function(message) {
                var obj = jQuery.parseJSON(message);
                $("#assetNewName", '.bootbox').val(obj.AssetName);
                $("#assetNewDescription", '.bootbox').val(obj.AssetDescription);
                $("#assetNewTag", '.bootbox').val(obj.AssetTag);
                $("#assetNewLocation", '.bootbox').val(obj.AssetLocation);
                $("#assetNewID", '.bootbox').val(assetID);
                $("#originalAssetTag", '.bootbox').val(obj.AssetTag);
            }
        });

    });

    //This function will update the corresponding label on the form control to let the user
    //know they have inputted data incorrectly into this field
    function OutputDataEntryError(id,message){
        //Get the current text of the label
        var labelText = $('#' + id + "Label").text() + "<br>";

        //Update the label to include the error message
        $('#' + id + "Error",'.bootbox').html(labelText + message + "<br>");

        //Set label colour to red to indicate an error
        $('#' + id + "Error",'.bootbox').css("color","red");
    }

</script>
@endsection