var assetTable;

$("document").ready(function(){
    //Populate Asset table on page load using Datables plugin
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
});

