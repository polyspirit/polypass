const PasswordGenerator = require('./PasswordGenerator');
const FormControl = require('./FormControl');

class PasswordGeneratorForm {

    constructor() {

        this.size = 12;

        this.fieldPass = document.querySelector('#text-generated-password');
        this.btnUpdate = document.querySelector('.js-generator-update');
        this.btnCopy = document.querySelector('.js-generator-copy');
        this.sizeRange = document.querySelector('.js-generator-size');
        this.sizeBadge = document.querySelector('.js-generator-size-badge');
        this.checkboxes = document.querySelectorAll('.js-generator-checkboxes .form-check-input');

        this.btnUpdate.onclick = this.generate.bind(this);
        this.btnCopy.onclick = this.copy.bind(this);
        this.sizeRange.oninput = this.changeSize.bind(this);

        this.generate();
    }

    changeSize() {

        this.size = this.sizeRange.value;
        this.sizeBadge.innerText = this.size;
    }

    copy() {
        formControls[this.fieldPass.name].copyValue();
    }

    generate() {

        const types = [];
        for (const checkbox of this.checkboxes) {
            if (checkbox.checked) {
                types.push(checkbox.name);
            }
        }

        const generator = new PasswordGenerator(this.size, types);

        this.fieldPass.value = generator.generate();
    }

}

module.exports = PasswordGeneratorForm;