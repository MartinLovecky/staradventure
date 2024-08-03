@use(Mlkali\Sa\Support\Enum)
@if ($member->logged)
    @redirect('/member'.'/'.$member->username.'/?message=', Enum::USER_LOGGED)
@endif
<article id="reset">
    <h2 class="major">
        @if(isset($member->memberID)) {{ "Zapomenutný Username" }} 
        @else {{ "Reset hesla" }} 
        @endif
    </h2>
    @form()
    <div class="fields">
        <div class="field"><input class="form-control text-white" type="email" name="email" placeholder="Email" required></div>
    </div>
    <ul class="actions">
        <li><button class="button primary" name="submit" type="submit">Poslat email</button></li>
    </ul>
    <input type='hidden' name="token" value="{{$encryption->encrypt($csrf)}}">
    <input type="hidden" id="g-recaptcha-response" name="grecaptcharesponse">
    <input type="hidden" name="action" value="validate_captcha">
    {{-- this is more readable than before--}}
    @if(isset($member->memberID))
    <input type="hidden" name="type" value={{"reset_user"}}>
    @else 
    <input type="hidden" name="type" value={{"reset_send"}} >
    @endif>
    </form>
    <script src="https://www.google.com/recaptcha/api.js?render=6LdKkYEUAAAAAE5Ykg8LY5gOPNXzgTyIG3FVuCqM"></script>
    <script src="@asset("js/recaptcha.js")"></script>
</article>