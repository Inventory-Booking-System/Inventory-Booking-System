import $ from 'jquery';
import * as bootstrap from 'bootstrap';
import moment from 'moment';
import bootbox from 'bootbox';
import toastr from 'toastr';
import * as adminlte from 'admin-lte';
import 'select2';
import * as Popper from '@popperjs/core';
import * as tempusDominus from '@eonasdan/tempus-dominus';

window.$ = $;
window.bootstrap = bootstrap;
window.moment = moment;
window.bootbox = bootbox;
window.toastr = toastr;
window.adminlte = adminlte;
window.Popper = Popper;
window.tempusDominus = tempusDominus;

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

//Livewire
document.addEventListener('livewire:load', function () {
    //Global Toastr Notifications
    toastr.options.progressBar = true;
    Livewire.on('alert', param => {
        toastr[param['type']](param['message']);
    });
    Livewire.on('showModal', param => {
        switch (param){
            case "create":
                $('#editModal').modal('show');
                break;
            case "edit":
                $('#editModal').modal('show');
                break;
            case "confirm":
                $('#confirmModal').modal('show');
                break;
            case "resolve":
                $('#resolveModal').modal('show');
                break;
        }
    });
    Livewire.on('hideModal', param => {
        switch (param){
            case "create":
                $('#editModal').modal('hide');
                break;
            case "edit":
                $('#editModal').modal('hide');
                break;
            case "confirm":
                $('#confirmModal').modal('hide');
                break;
            case "resolve":
                $('#resolveModal').modal('hide');
                break;
        }
    });
});

/**
 * Fix for Select2 search box auto focus when using jQuery 3.6.0
 */
$(document).on('select2:open', () => {
    document.querySelector('.select2-container--open .select2-search__field').focus();
});