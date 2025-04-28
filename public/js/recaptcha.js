grecaptcha.ready(function () {
    grecaptcha.execute('6LfIYSYrAAAAAINNK1T7QCPnkWzi-CHS1qzsztTG', { action: 'validate_captcha' })
        .then(function (token) {
            document.getElementById('g-recaptcha-response').value = token;
        });
});