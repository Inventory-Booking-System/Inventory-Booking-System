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
