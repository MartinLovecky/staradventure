@use(Mlkali\Sa\Http\Response)
@if ($member->logged)
    @php
        return new Response('/member'.'/'.$member->username.'/?message=','warning.Stránku login nelze otevřít když jste přihlášen');
    @endphp
@endif
<article id="login">
    <h2 class="major">Přihlášení</h2>
    {!! 
        $form->options(['target'=>'requestHandler'])
        ->vars(['memberController'=>$memberController, 'request' => $request])
        ->run() 
    !!}
    <div class="fields">
        <div class="field"><input class="form-control text-white" type="text" name="username" value="@isset($_SESSION['old_username']){{$_SESSION['old_username']}}@endisset" placeholder="Username"></div>
        <div class="field"><input class="form-control text-white" type="password" name="password" placeholder="Heslo" autocomplete="new-password"></div>
        <div class="field half">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="formCheck-3" name="remember">
                <label class="form-check-label text-muted" for="formCheck-3">Pamatovat si mě</label>
            </div>
        </div>
    </div>
        <ul class="actions">
            <li><button class="button primary" name="submit" type="submit">Přihlásit</button></li>
        </ul>
        <p class="text-muted text-center">Nemáte účet?<a href="/register#register">&nbsp;přidejte se</a>.<br>
            <a href="/reset#reset">Zapomenutné heslo?</a><br>
            <a href="/reset?id={{base64_encode('forgotenUser')}}#reset">Zapomenutný Username?</a>
        </p>
        <input type='hidden' name="token" value="{{$enc->encrypt($csrf)}}">
        <input type="hidden" id="g-recaptcha-response" name="grecaptcharesponse">
    	<input type="hidden" name="action" value="validate_captcha">
        <input type="hidden" name="type" value="login"> 
    </form>
    <script src="https://www.google.com/recaptcha/api.js?render=6LclhVIjAAAAAAUcH7r8tvwJl3GIUg8bLJmr2alF"></script>
    <script src="@asset("js/recaptcha.js")"></script>
</article>