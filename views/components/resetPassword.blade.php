@use(Mlkali\Sa\Support\Messages)
@if ($member->logged)
@redirect(
    "/member/{$member->username}?message=", 
    Messages::WARNING_USER_LOGGED, 
    '#member'
)
@endif
<article id="reset">
    <h2>Reset hesla</h2>
    @form()
    <div class="fields">
        <div class="field">
            <input class="form-control text-white" type="email" name="email" placeholder="Email" required>
        </div>
    </div>
    <ul class="actions">
        <li><button class="button primary" name="submit" type="submit">Poslat email</button></li>
    </ul>
    <input type='hidden' name="token" value="{{  $csrf  }}">
    <input type="hidden" id="g-recaptcha-response" name="grecaptcharesponse">
    <input type="hidden" name="action" value="validate_captcha">
    <input type="hidden" name="type" value="passwordResetSend">
    </form>
    <script src="https://www.google.com/recaptcha/api.js?render={{$captha}}"></script>
    <script src="@asset("js/recaptcha.js")"></script>
</article>
