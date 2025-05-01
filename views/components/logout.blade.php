@use(Mlkali\Sa\Support\Messages)
@if($member->logged)
{!! $memberController->logout() !!}
@else
@redirect('/index?message=', Messages::DANGER_USER_NOT_LOGGED)
@endif
