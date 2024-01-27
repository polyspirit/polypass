class PasswordGenerator {

    constructor(length = 12, charsTypes = []) {

        this.length = length;
        this.chars = '';

        this.types = {
            'digits': '0123456789',
            'letters-lower': 'abcdefghijklmnopqrstuvwxyz',
            'letters-upper': 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'symbols': '!@#$%-_',
            'symbols-extended': '^&*()+~№;%:?=[]{}\|/,.<>'
        };

        if (charsTypes.length === 0) {
            for (let type in this.types) {
                if (type !== 'symbolsExtended') {
                    this.chars += this.types[type];
                }
            }
        } else {
            charsTypes.forEach(type => {
                if (this.types[type]) {
                    this.chars += this.types[type];
                }
            });
        }
    }

    generate() {

        let password = '';

        for (let i = 0; i <= (this.length - 1); i++) {
            let randomNumber = Math.floor(Math.random() * this.chars.length);
            password += this.chars.substring(randomNumber, randomNumber + 1);
        }

        return password;
    }

}

module.exports = PasswordGenerator;
