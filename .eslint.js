module.exports = {
    parserOptions: {
        ecmaVersion: 2017
    },
    extends: [
        'standard',
        'plugin:vue/recommended'
    ],
    plugins: [
        'vue'
    ],
    env: {
        jest: true
    },
    globals: {

    },
    rules: {
    },
    overrides: [{
        files: ['**/*.js', '**/*.vue'],
        excludedFiles: [ '*.test.js', 'public/*.js', 'vendor/**' ]
    }]
}
