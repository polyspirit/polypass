class Button {

    constructor() {

        const fakeButtons = document.querySelectorAll('.js-fake-button');

        for (const button of fakeButtons) {
            button.onclick = event => {
                this.submitHiddenForm(event, button.dataset.id);
            };
        }

    }

    submitHiddenForm(event, id) {

        event.preventDefault();

        const form =  document.querySelector('.hidden-form#' + id);
        if (form) {
            form.submit();
        }
    }

}

module.exports = Button;