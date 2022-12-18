@use(Mlkali\Sa\Http\Response)
@if (!$member->logged)
    @php
        return new Response('/member'.'/'.$member->username.'/?message=','warning.Stránku upravit profil nelze otevřít když nejste přihlášen');
    @endphp
@endif
<article id="updatemember">
    <h2 class="major">Upravit profil</h2>
    {!! 
	    $form->options(['target'=>'requestHandler', 'enctype' => 'multipart/form-data'])
		->vars(['requestController'=>$requestController, 'request' => $request])
		->run() 
	!!}
        <div class="fields">
            <div class="field half"><input class="form-control text-white" type="text" name="username" placeholder="{{$member->username}}*"></div>
            <div class="field half"><input class="form-control text-white" type="email" name="email" placeholder="{{$member->email}}*"></div>
            <div class="field half"><input class="form-control text-white" type="text" name="name" placeholder="@isset($member->memberName){{$member->memberName}}*@endisset{{'Jméno*'}}"></div>
            <div class="field half"><input class="form-control text-white" type="text" name="surname" placeholder="@isset($member->memberSurname){{$member->memberSurname}}*@endisset{{'Příjmení*'}}"></div>
            <div class="field half"><label for="age">Datum narození:*</label><input class="form-controll text-dark" type="date" name="age"></div>
            <div class="field half"><label for="avatar">Avatar: jpg/png/jpeg</label><input type="file" name="avatar" required></div>
            <div class="field"><input class="form-control text-white" type="text" name="location" placeholder="@isset($member->location){{$member->location}}*@endisset{{'Město*'}}"></div>
            <div class="field">
                <div class="form-check"><input class="form-check-input" type="radio" id="formCheck-2" name="visible"><label class="form-check-label" for="formCheck-2"><a title="vaše informace budou veřejné" href="/terms#terms">Viditelné <b style="color:#e72d2d">**</b></a></label></div>
             </div>     
        </div>
        <ul class="actions">
            <li><button class="button primary" name="submit" value="submit" type="submit">Upravit Info</button></li>
        </ul>
        <p class="text-muted text-center">* Pole není povinné <br>** Informace budou viditelné pro všechny uživatele<br>Zpět na profil&nbsp;<a href="/member/{{$member->username}}">{{$member->username}}</a></p>
        <input type='hidden' name="token" value="{{$enc->encrypt($csrf)}}">
        <input type="hidden" id="g-recaptcha-response" name="grecaptcharesponse">
    	<input type="hidden" name="action" value="validate_captcha">
        <input type="hidden" name="type" value="update_member">
    </form>
    <script src="https://www.google.com/recaptcha/api.js?render=6LclhVIjAAAAAAUcH7r8tvwJl3GIUg8bLJmr2alF"></script>
    <script src="@asset("js/recaptcha.js")"></script>
</article>