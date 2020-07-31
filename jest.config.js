module.exports = {
    moduleFileExtensions: ['js', 'vue', 'json'],
    transform: {
        '^.+\\.js$': 'babel-jest',
        '^.+\\.vue$': 'vue-jest',
        '.+\\.(css|styl|less|sass|scss|svg|png|jpg|ttf|woff|woff2)$': 'jest-transform-stub'
    },
    moduleNameMapper: {
        '^@/(.*)$': '<rootDir>/resources/js/$1',
        '^vue$': 'vue/dist/vue.common.js'
    },
    snapshotSerializers: ['jest-serializer-vue'],
    // collectCoverage: true,
    collectCoverageFrom: [
        '<rootDir>/resources/js/components/**/*.vue',
        '<rootDir>/resources/js/views/**/*.vue'
    ],
    verbose: true,
    notify: false,
    setupFiles: [
        'core-js',
        '<rootDir>/jest.globals.js'
    ]
};
