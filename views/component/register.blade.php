@use(Mlkali\Sa\Http\Response)
@if ($member->logged)
    @php
        return new Response('/member'.'/'.$member->username.'/?message=','warning.Stránku register nelze otevřít když jste přihlášen');
    @endphp
@endif
<article id="register">
    <h2 class="major">Přidej se</h2>
    {!! 
	    $form->options(['target'=>'requestHandler','class'=>'text-left'])
		->vars(['requestController'=>$requestController])
		->run() 
	!!}
        <div class="fields">
            <div class="field half"><input class="form-control text-white" type="text" name="username" placeholder="Username" value="@isset($_SESSION['old_username']){{ $_SESSION['old_username'] }}@endisset" required></div>
            <div class="field half"><input class="form-control text-white" type="email" name="email" placeholder="Email" required></div>
            <div class="field"><input class="form-control text-white" type="password" name="password" placeholder="Heslo" passwordrules="required: upper, lower, digit, [-().&@?'#,/&quot;+]; minlength: 25; max-consecutive: 2" autocomplete="new-password" required></div>
            <div class="field"><input class="form-control text-white" type="password" name="password_again" placeholder="Heslo (znovu)" passwordrules="required: upper, lower, digit, [-().&@?'#,/&quot;+]; max-consecutive: 2" required></div>
            <div class="field half">
                <div class="form-check"><input class="form-check-input" type="radio" id="formCheck-2" name="terms" required><label class="form-check-label" for="formCheck-2"><a href="/terms#terms">Terms of Service</a></label></div>
            </div>
            <div class="field half">
                <div class="form-check"><input class="form-check-input" type="radio" id="formCheck-1" name="vops" required><label class="form-check-label" for="formCheck-1"><a href="/vop#vop">Privacy Policy</a></label></div>
            </div>
        </div>
        <ul class="actions">
            <li><button class="button primary" name="submit" value="submit" type="submit">Registrovat</button></li>
        </ul>
        <p class="text-muted text-center">Máte již účet?<a href="/login#login">&nbsp;přihlásit se</a></p>
        <input type='hidden' name="token" value="{{$csrf}}">
        <input type="hidden" id="g-recaptcha-response" name="grecaptcharesponse">
    	<input type="hidden" name="action" value="validate_captcha">
        <input type="hidden" name="type" value="register">
    </form>
    <script src="https://www.google.com/recaptcha/api.js?render=6LclhVIjAAAAAAUcH7r8tvwJl3GIUg8bLJmr2alF"></script>
    <script src="@asset("js/recaptcha.js")"></script>
</article>