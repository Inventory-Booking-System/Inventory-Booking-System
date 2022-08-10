<div
    x-init="new tempusDominus.TempusDominus(document.getElementById('pickerSideBySide', {display: {sideBySide: true}}));"
>

    <div
        class='input-group'
        id='pickerSideBySide'
        data-td-target-input='nearest'
        data-td-target-toggle='nearest'
    >
   <input
     id='pickerSideBySideInput'
     type='text'
     class='form-control'
     data-td-target='#pickerSideBySide'
   />
   <span
     class='input-group-text'
     data-td-target='#pickerSideBySide'
     data-td-toggle='datetimepicker'
   >
     <span class='fa-solid fa-calendar'></span>
   </span>
 </div>
</div>
