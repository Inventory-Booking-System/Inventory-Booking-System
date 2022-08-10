var userTable;

$("document").ready(function(){
    //Populate User table on page load using Datables plugin
    userTable = $('#usersTable').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "users",
        "pageLength": 25,
        columns: [
            {
                data: function (row) {
                    return '<a href="/users/' + row.id + '">' + row.forename + '</a>';
                },
                name: 'forename'
            },
            {
                data: function (row) {
                    return '<a href="/users/' + row.id + '">' + row.surname + '</a>';
                },
                name: 'surname'
            },
            {data: 'email', name: 'email'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    //Delete asset from database
    $("#userTable").on('click', '.archiveUser', function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        //Get the id of the asset we are deleting
        var id = $(this).closest("tr").attr("id");

        var modal = bootbox.dialog({
            message: $(".archiveUser").html(),
            size: "large",
            title: "Archive User",
            buttons: [
            {
                label: "Archive",
                className: "btn btn-danger pull-right",
                callback: function(result) {
                    //Send ajax request to the server to save to database and then update the table on the website accordingly
                    jQuery.ajax({
                        type: "DELETE",
                        url: "users/"+id,
                        dataType: 'json',
                        success: function(data) {
                            //Popup to tell the user the action has completed successfully
                            toastr.success(data['name'] + ' has been archived');

                            //Re-populate the table
                            assetTable.ajax.reload();
                        },
                        error: function(data){
                            toastr.error('User could not be archived');
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
