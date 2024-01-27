const Copier = require('./Copier');

class FormControl {

    constructor(control) {

        this.control = control;
        this.wrapper = control.closest('.form-control-wrapper');
        this.label = this.wrapper.querySelector('.form-label');
        this.elemCopyIcon = this.wrapper.querySelector('.copy-icon');

        if (this.elemCopyIcon) {
            this.elemCopyIcon.addEventListener('click', e => {
                this.copyValue();
            });
        }
    }

    copyValue() {
        Copier.copy(this.control.value);
        this.showNotice();
    }

    showNotice() {
        const notice = document.createElement('span');
        notice.classList.add('form-notice', 'text-info');
        notice.innerText = this.label.dataset.copied;

        this.label.appendChild(notice);

        setTimeout(() => {
            notice.remove();
        }, 2000);
    }

}

module.exports = FormControl;
