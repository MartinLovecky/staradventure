@set($memberController = $container->get(Mlkali\Sa\Controllers\MemberController::class))
@php  $memberController->logout();  @endphp