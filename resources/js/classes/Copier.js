const Notifier = require('./Notifier');

class Copier {

    constructor() {

        const copyItems = document.querySelectorAll('.js-copy-text, .js-copy-data');

        for (const item of copyItems) {
            item.addEventListener('click', () => {
                if (item.classList.contains('js-copy-text')) {
                    this.constructor.copy(item.innerText);
                } else {
                    this.constructor.copy(item.dataset.value);
                }
            })
        }
    }

    static copy(text) {
        try {
            navigator.clipboard.writeText(text);
        } catch (error) {
            // nothing
        }

        Notifier.showNotice((document.documentElement.lang === 'ru') ? 'Скопировано' : 'Copied');
    }
}

module.exports = Copier;
