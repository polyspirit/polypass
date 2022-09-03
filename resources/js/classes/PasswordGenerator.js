class PasswordGenerator {

    constructor(length = 12, charsTypes = []) {

        this.length = length;
        this.chars = '';

        this.types = {
            digits: '0123456789',
            lettersLower: 'abcdefghijklmnopqrstuvwxyz',
            lettersUpper: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            symbols: '!@#$%-_',
            symbolsExtended: '^&*()+~№;%:?=[]{}\|/,.<>'
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

        for (let i = 0; i <= this.length; i++) {
            let randomNumber = Math.floor(Math.random() * this.chars.length);
            password += this.chars.substring(randomNumber, randomNumber + 1);
        }

        return password;
    }

}

module.exports = PasswordGenerator;