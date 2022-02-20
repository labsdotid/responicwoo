const path = require('path');

module.exports = {
    entry: './assets/js/index.js',
    output: {
        path: path.resolve(__dirname, 'assets/js/'),
        filename: 'bundle.js',
    },
};