const PasswordGenerator = require('./PasswordGenerator');

class Password {

    constructor(elemPassword) {

        this.showed = false;

        this.elemInput = elemPassword.querySelector('.form-control');
        this.elemIcons = elemPassword.querySelector('.password-icons');
        this.elemEyeIcon = this.elemIcons.querySelector('.password-eye-icon');
        this.elemGenIcon = this.elemIcons.querySelector('.password-gen-icon');

        this.elemEyeIcon.addEventListener('click', this.changeType.bind(this));
        if (this.elemGenIcon) {
            this.elemGenIcon.addEventListener('click', this.generateRandom.bind(this));
        }
    }

    changeType() {

        this.elemEyeIcon.classList.toggle('fa-eye');
        this.elemEyeIcon.classList.toggle('fa-eye-slash');

        if (this.showed) {
            this.elemInput.type = 'password';
        } else {
            this.elemInput.type = 'text';
        }

        this.showed = !this.showed;
    }

    generateRandom() {

        const generator = new PasswordGenerator();
        this.elemInput.value = generator.generate();
    }
}

module.exports = Password;