import Password from './classes/Password';

// passwords 
const elemsPasswords = document.querySelectorAll('.password-wrapper');
for (const elemPassword of elemsPasswords) {
    new Password(elemPassword);
}