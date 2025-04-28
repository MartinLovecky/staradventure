@use(Mlkali\Sa\Support\Enum)
@if ($member->logged)
<<<<<<< HEAD
@redirect("/member/{$member->username}?message=", Messages::USER_LOGGED, '#member')
=======
@redirect("/member/{$member->username}?message=", Enum::USER_LOGGED, '#member')
>>>>>>> 910359bbb8bba1455894d3acb556ad9a3f3852a6
@endif
<article id="reset">
    <h2>Zapomenutný Username</h2>
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
    <input type="hidden" name="type" value="forgotenUsername">
    </form>
    <script src="https://www.google.com/recaptcha/api.js?render={{$captha}}"></script>
    <script src="@asset(" js/recaptcha.js")"></script>
</article>
