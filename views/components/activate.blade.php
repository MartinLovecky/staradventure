@use(Mlkali\Sa\Support\Enum)
@if ($selector->queryID)
    {{ $memberController->activate() }}
@else
    {{ $response->redirect('/index?message=', sprintf(Enum::REQUETS_REGISTER, null))  }}
@endif