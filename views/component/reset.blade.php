@use(Mlkali\Sa\Http\Response)
@use(Mlkali\Sa\Support\Enum)
@if ($member->logged)
    @php
        return new Response('/member'.'/'.$member->username.'/?message=', Enum::USER_LOGGED);
    @endphp
@endif
<article id="reset">
    <h2 class="major">@if(isset($query)) {{ "Zapomenutné Username" }} @else {{ "Reset hesla" }} @endif</h2>
    {!! 
        $form->options(['target'=>'requestHandler'])
        ->vars(['memberController'=>$memberController, 'request' => $request])
        ->run() 
    !!}
    <div class="fields">
        <div class="field"><input class="form-control text-white" type="email" name="email" placeholder="Email" required></div>
    </div>
    <ul class="actions">
        <li><button class="button primary" name="submit" type="submit">Poslat email</button></li>
    </ul>

    <input type='hidden' name="token" value="{{$enc->encrypt($csrf)}}">
    <input type="hidden" id="g-recaptcha-response" name="grecaptcharesponse">
    <input type="hidden" name="action" value="validate_captcha">
    <input type="hidden" name="type" value= @if(isset($query)) {{ "reset_user" }} @else {{"reset_send"}} @endif>
    </form>
    <script src="https://www.google.com/recaptcha/api.js?render=6LclhVIjAAAAAAUcH7r8tvwJl3GIUg8bLJmr2alF"></script>
    <script src="@asset("js/recaptcha.js")"></script>
</article>