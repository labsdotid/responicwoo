module.exports = {
    prefix: 'responicwoo-',
    important: true,
    content: [
        './*.php',
        './*/*.php'
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms')
    ],
}