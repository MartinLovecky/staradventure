@use(Mlkali\Sa\Http\Response)
@use(Mlkali\Sa\Support\Enum)
@set($member = $container->get(Mlkali\Sa\Database\Entity\Member::class))
@set($memberController = $container->get(Mlkali\Sa\Controllers\MemberController::class))
@set($form = $container->get(Mlkali\Sa\Html\Form::class))
@set($enc = $container->get(Mlkali\Sa\Support\Encryption::class))
@set($request = $container->get(Mlkali\Sa\Http\Request::class))
@set($selector = $container->get(Mlkali\Sa\Support\Selector::class))
@isset($selector->queryID)
<article id="newpassword">
    <h2 class="major">Nové heslo</h2>
    {!! 
        $form->options(['target'=>'requestHandler','class'=>'text-left'])
        ->vars(['memberController'=>$memberController, 'request' => $request])
        ->run() 
    !!}
    <div class="fields">
        <div class="field"><input class="form-control text-white" type="password" name="password" placeholder="Heslo" passwordrules="required: upper, lower, digit, [-().&@?'#,/&quot;+]; minlength: 25; max-consecutive: 2" autocomplete="new-password" required></div>
        <div class="field"><input class="form-control text-white" type="password" name="password_again" placeholder="Heslo (znovu)" passwordrules="required: upper, lower, digit, [-().&@?'#,/&quot;+]; max-consecutive: 2" required></div>
    </div>
    <ul class="actions">
        <li><button class="button primary" name="submit" value="submit" type="submit">Změnit heslo</button></li>
    </ul>
    <input type='hidden' name="token" value="{{$csrf}}">
    <input type="hidden" id="g-recaptcha-response" name="grecaptcharesponse">
    <input type="hidden" name="action" value="validate_captcha">
    <input type="hidden" name="type" value="new_password">
    <input type="hidden" name="etoken" value="{{ base64_decode($selector->queryID) }}">
</form>
<script src="https://www.google.com/recaptcha/api.js?render=6LclhVIjAAAAAAUcH7r8tvwJl3GIUg8bLJmr2alF"></script>
<script src="@asset("js/recaptcha.js")"></script>
</article>
@else
@php
    return new Response('newpassword?message=', Enum::IVALID_URL, '#newpassword');
@endphp

@endisset