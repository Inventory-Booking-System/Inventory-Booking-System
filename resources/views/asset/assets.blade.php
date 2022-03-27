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
    <li class="nav-item">
        <a id="deleteAsset" href="#" class="nav-link">
        <i class="nav-icon fas fa-trash-alt"></i>
        <p>
            Delete Asset
        </p>
        </a>
    </li>
    <li class="nav-item">
        <a id="modifyAsset" href="#" class="nav-link">
        <i class="nav-icon fas fa-undo-alt"></i>
        <p>
            Modify Asset
        </p>
        </a>
    </li>
@endsection

@include('asset.modals.add')

@section('scripts')
<script type="text/javascript">
    //Add new asset to database
    $('#addAsset').on('click', function (e) {
        var modal = bootbox.dialog({
            message: $(".addAsset").html(),
            size: "large",
            title: "Add New Asset",
            buttons: [
            {
                label: "Save",
                className: "btn btn-primary pull-right",
                callback: function(result) {
                    //Get the data that was input into each field
                    var assetName = $('#assetName', '.bootbox').val();
                    var assetDescription = $('#assetDescription', '.bootbox').val();
                    var assetTag = $('#assetTag', '.bootbox').val();
                    var assetLocation = $('#assetLocation','.bootbox').val();
                    var assetCost = $('#assetCost','.bootbox').val();
                    var assetBookable = $('#assetBookable','.bootbox').prop('checked');
                    var validationError = true;

                    //Send ajax request to the server to save to database and then update the table on the website accordingly
                    jQuery.ajax({
                        type: "POST",
                        url: "asset",
                        async: false,
                        data: {assetName: assetName, assetDescription: assetDescription, assetTag: assetTag, assetLocation: assetLocation, assetCost: assetCost, assetBookable: assetBookable},
                        success: function(message) {
                            //If message returned is "success" then insert new asset into table and add to other dropdowns
                            if(message == "Success"){
                                validationError = false;
                                toastr.success(assetName + ' has been created');

                                //We need to get the ID of the asset we have just created
                                jQuery.ajax({
                                    type: "POST",
                                    url: "index.php/manageassets/getAssetID",
                                    data: {assetTag: assetTag},
                                    success: function(assetID) {
                                        var obj = JSON.parse(assetID);
                                        $("#assetsTable > tbody").append("<tr id='" + obj.AssetID + "'><td>" + assetName + "</td><td>" + assetDescription + "</td><td>" + assetTag + "</td><td>" + assetLocation + "</td></tr>");

                                        //Add to the delete asset dropdown
                                        $("<option id='Delete-" + obj.AssetID + "'>" + assetName + " (" + assetTag + ")</option>").appendTo("#assetToDelete");
                                        $("<option id='Modify-" + obj.AssetID + "'>" + assetName + " (" + assetTag + ")</option>").appendTo("#assetToModify");
                                    }
                                });

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
                                $('#errorText','.bootbox').html(message + "<br>");
                            }
                        },
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
    $('#deleteAsset').on('click', function (e) {
        var modal = bootbox.dialog({
            message: $(".deleteAsset").html(),
            size: "large",
            title: "Delete Asset",
            buttons: [
            {
                label: "Save",
                className: "btn btn-primary pull-right",
                callback: function(result) {
                    //Get the data that was input into each field
                    var assetName = $(".modal-body #assetToDelete").val();
                    console.log(assetName);

                    if(assetName != null){
                        var assetID = $(".modal-body #assetToDelete").children(":selected").attr("id").split("-")[1];
                        //Send ajax request to the server to save to database and then update the table on the website accordingly
                        jQuery.ajax({
                            type: "POST",
                            url: "index.php/manageassets/deleteAsset",
                            data: {assetID: assetID},
                            success: function(message) {
                                //If message returned is "success" then insert new asset into table
                                if(message == "Success"){
                                    //Remove asset from the table that was just deleted
                                    $("#" + assetID).remove();

                                    //Remove asset from ModifyAsset List
                                    $("#Modify-" + assetID).remove();
                                    $("#Delete-" + assetID).remove();

                                    //Update Table
                                    jQuery.ajax({
                                        type: "POST",
                                        url: "index.php/manageassets/getListOfAssets",
                                        success: function(message){
                                            $("#assetsTable").html(message);
                                        }
                                    });

                                    toastr.success(assetName + " has been deleted");
                                }else{
                                    toastr.error(message);
                                    $('#errorTextDelete','.bootbox').html(message + "<br>");
                                }
                            }
                        });
                    }else{
                        $('#errorTextDelete','.bootbox').html("Select an asset to delete<br>");
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
</script>
@endsection