{{-- Redirect is not necesary without $query index.blade.php is shown --}}
@isset($query)
<article id="newpassword">
    <h2 class="major">Nové heslo</h2>
    {!! 
        $form->options(['target'=>'requestHandler','class'=>'text-left'])
        ->vars(['requestController'=>$requestController])
        ->run() 
    !!}
    <div class="fields">
        <div class="field"><input class="form-control text-white" type="password" name="password" placeholder="Heslo" passwordrules="required: upper, lower, digit, [-().&@?'#,/&quot;+]; minlength: 25; max-consecutive: 2" autocomplete="new-password" required></div>
        <div class="field"><input class="form-control text-white" type="password" name="password_again" placeholder="Heslo (znovu)" passwordrules="required: upper, lower, digit, [-().&@?'#,/&quot;+]; max-consecutive: 2" required></div>
    </div>
    <ul class="actions">
        <li><button class="button primary" name="submit" value="submit" type="submit">Změnit heslo</button></li>
    </ul>
    @csrf
    <input type="hidden" id="g-recaptcha-response" name="grecaptcharesponse">
    <input type="hidden" name="action" value="validate_captcha">
    <input type="hidden" name="type" value="new_password">
    <input type="hidden" name="etoken" value="{{ base64_decode($query) }}">
</form>
<script src="https://www.google.com/recaptcha/api.js?render=6LclhVIjAAAAAAUcH7r8tvwJl3GIUg8bLJmr2alF"></script>
<script src="@asset("js/recaptcha.js")"></script>
</article>
@endisset