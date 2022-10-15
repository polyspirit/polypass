class Notifier {

    static showNotice(text) {

        const wrapper = document.querySelector('.js-notices');

        const notice = document.createElement('div');
        notice.classList.add('alert', 'alert-success');
        notice.setAttribute('role', 'alert');
        notice.innerText = text;

        wrapper.appendChild(notice);
        
        setTimeout(() => {
            notice.remove();
        }, 2000);
    }
}

module.exports = Notifier;