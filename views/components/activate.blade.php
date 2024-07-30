@use(Mlkali\Sa\Support\Enum)
{{-- //NOTE - not ideal should be fixed soon --}}
@set($memberID = $selector->getQueryMessage("id"))
@if ($memberID)
    {{ $memberController->activate() }}
@else
    {{ $response->redirect('/index?message=', sprintf(Enum::REQUETS_REGISTER, null))  }}
@endif