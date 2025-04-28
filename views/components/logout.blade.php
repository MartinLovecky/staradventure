@use(Mlkali\Sa\Support\Enum)
@if($member->logged)
{!! $memberController->logout() !!}
@else
@redirect('/index?message=', Messages::USER_NOT_LOGGED)
@endif
