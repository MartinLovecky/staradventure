@use(Mlkali\Sa\Http\Response)
@use(Mlkali\Sa\Support\Enum)
@set($member = $container->get(Mlkali\Sa\Database\Entity\Member::class))
@set($memberController = $container->get(Mlkali\Sa\Controllers\MemberController::class))
@set($enc = $container->get(Mlkali\Sa\Support\Encryption::class))
@set($selector = $container->get(Mlkali\Sa\Support\Selector::class))
@set($message = $container->get(Mlkali\Sa\Support\Messages::class))
@if (!$member->logged)
  @php
    return new Response('/?message=', Enum::USER_NOT_LOGGED, '#');
  @endphp 
@endif
@if ($member->permission !== 'admin')
  @php
    return new Response('/?message=', Enum::USER_PERMISSION, '#');
  @endphp 
@endif
@if ($message->hasAny())
  @component('component.message')@endcomponent
@endif
<table class="table table-bordered table-dark">
    <thead>
      <tr>
        <th scope="col">#ID</th>
        <th scope="col">UserName</th>
        <th scope="col">Permission</th>
        <th scope="col">#</th>
      </tr>
    </thead>
    
    <tbody>
      @foreach ($memberController->allMembers() as $key => $data)
      <tr>
        <th scope="row">{{$data['id']}}</th>
        <td>{{$data['username']}}</td>
        <td>
          <div class="dropdown">
            <button class="btn btn-default dropdown-toggle"
              type="button"
              id="dropdownMenuButton"
              data-bs-toggle="dropdown"
              data-boundary="window"
              aria-haspopup="true"
              aria-expanded="false">
              <span>{{$data['permission']}}</span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <a href="/usertable?id={{$enc->encrypt($data['member_id'])}}&action={{$enc->encrypt('visitor')}}" class="dropdown-item">visitor</a>
              <a href="/usertable?id={{$enc->encrypt($data['member_id'])}}&action={{$enc->encrypt('user')}}" class="dropdown-item">user</a>
              <a href="/usertable?id={{$enc->encrypt($data['member_id'])}}&action={{$enc->encrypt('rewriter')}}" class="dropdown-item">rewriter</a>
              <a href="/usertable?id={{$enc->encrypt($data['member_id'])}}&action={{$enc->encrypt('admin')}}" class="dropdown-item">admin</a>
            </ul>
          </div>
        </td>
        <td><a class="text-danger" href="/usertable?id={{$data['id']}}&action={{$enc->encrypt('delete')}}">Delete</a></td>
      </tr>
      @endforeach
    </tbody>
  </table>
@php
  if(isset($selector->queryAction)){
    switch ($enc->decrypt($selector->queryAction)) {
      case 'visitor':
        $memberController->permission('visitor', $enc->decrypt($selector->queryID));
      break;
      case 'user':
        $memberController->permission('user', $enc->decrypt($selector->queryID));
      break;
      case 'rewriter':
        $memberController->permission('rewriter', $enc->decrypt($selector->queryID));
      break;
      case 'admin':
        $memberController->permission('admin', $enc->decrypt($selector->queryID));
      break;
      case 'delete':
        $memberController->delete($enc->decrypt($selector->queryID));
      break;
    }
  }
@endphp