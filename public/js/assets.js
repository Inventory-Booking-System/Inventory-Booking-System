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
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
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
});

