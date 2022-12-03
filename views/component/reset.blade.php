@use(Mlkali\Sa\Http\Response)
@if ($member->logged)
    @php
        return new Response('/member'.'/'.$member->username.'/?message=','warning.Stránku reset nelze otevřít když jste přihlášen');
    @endphp
@endif
<article id="reset">
    <h2 class="major">@if(isset($query)) {{ "Zapomenutné Username" }} @else {{ "Reset hesla" }} @endif</h2>
    {!! 
        $form->options(['target'=>'requestHandler','class'=>'text-left'])
        ->vars(['requestController'=>$requestController])
        ->run() 
    !!}
    <div class="fields">
        <div class="field"><input class="form-control text-white" type="email" name="email" placeholder="Email" required></div>
    </div>
    <ul class="actions">
        <li><button class="button primary" name="submit" type="submit">Poslat email</button></li>
    </ul>

    @csrf
    <input type="hidden" id="g-recaptcha-response" name="grecaptcharesponse">
    <input type="hidden" name="action" value="validate_captcha">
    <input type="hidden" name="type" value= @if(isset($query)) {{ "reset_user" }} @else {{"reset_send"}} @endif>
    </form>
    <script src="https://www.google.com/recaptcha/api.js?render=6LdKkYEUAAAAAE5Ykg8LY5gOPNXzgTyIG3FVuCqM"></script>
    <script src="@asset("js/recaptcha.js")"></script>
</article>