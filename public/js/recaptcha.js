grecaptcha.ready(function () {
    document.getElementById('contact-form').addEventListener("submit", function (event) {
        event.preventDefault();
        grecaptcha.execute('6LclhVIjAAAAAAUcH7r8tvwJl3GIUg8bLJmr2alF', { action: 'validate_captcha' }).then(function (token) {
            document.getElementById("g-recaptcha-response").value = token;
            document.getElementById('contact-form').submit();
        });
    }, false);
});