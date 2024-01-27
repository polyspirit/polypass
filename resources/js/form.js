import FormControl from './classes/FormControl';
import Password from './classes/Password';

// all form controls
window.formControls = {};
const formControlsElems = document.querySelectorAll('input.form-control, .form-select');
for (const elem of formControlsElems) {
    formControls[elem.name] = new FormControl(elem);
}

// passwords
const elemsPasswords = document.querySelectorAll('.password-wrapper');
for (const elemPassword of elemsPasswords) {
    new Password(elemPassword);
}
