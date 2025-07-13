class CredentialRemote {

    constructor() {

        this.portField = document.querySelector('.js-port-field');
        this.protocolSelect = document.querySelector('.js-protocol-select');

        if (this.portField && this.protocolSelect) {
            this.protocolSelect.onchange = this.changePort.bind(this);

            if (!this.portField.value) {
                this.changePort();
            }
        }
    }

    changePort() {

        const selectedOption = this.protocolSelect.options[this.protocolSelect.selectedIndex];
        this.portField.value = selectedOption.dataset.port;
    }
}

export default CredentialRemote;