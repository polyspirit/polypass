class Password {

    constructor(elemPassword) {

        this.showed = false;

        this.elemInput = elemPassword.querySelector('.form-control');
        this.elemEye = elemPassword.querySelector('.password-eye');
        this.elemEyeIcon = this.elemEye.querySelector('.password-eye-icon');

        this.elemEye.addEventListener('click', this.changeType.bind(this));
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
}

module.exports = Password;