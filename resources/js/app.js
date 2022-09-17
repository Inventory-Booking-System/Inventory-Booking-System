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
    Livewire.on('showModal', () => {
        console.log("HERE");
        $('#exampleModal').modal('show');
    });
    Livewire.on('hideModal', () => {
        console.log("HERE");
        $('#exampleModal').modal('hide');
    });
});

// let modalsElement = document.getElementById('laravel-livewire-modals');

// modalsElement.addEventListener('hidden.bs.modal', () => {
//     Livewire.emit('resetModal');
// });

// $( document ).ready(function() {
//     Livewire.on('showModal', () => {
//         let modal = Modal.getInstance(modalsElement);

//         if (!modal) {
//             modal = new Modal(modalsElement);
//         }

//         modal.show();
//     });

//     Livewire.on('hideModal', () => {
//         let modal = Modal.getInstance(modalsElement);

//         modal.hide();
//     });
// })
