import FormControl from './classes/FormControl';
import Password from './classes/Password';

// all form controls
const formControls = document.querySelectorAll('input.form-control');
const formSelects = document.querySelectorAll('.form-select');
for (const control of formControls) {
    new FormControl(control);
}
for (const select of formSelects) {
    new FormControl(select);
}

// passwords 
const elemsPasswords = document.querySelectorAll('.password-wrapper');
for (const elemPassword of elemsPasswords) {
    new Password(elemPassword);
}