@use(Mlkali\Sa\Http\Response)
@if (!$member->logged)
  @php
    return new Response('/?message=','danger.Nemáte přístup k zobrazení stránky','#');
  @endphp 
@endif
@if ($member->permission !== 'admin')
  @php
    return new Response('/?message=','danger.Nemáte přístup k zobrazení stránky','#');
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
  if(isset($selector->secondQueryValue)){
    switch ($enc->decrypt($selector->secondQueryValue)) {
      case 'visitor':
        $memberController->permission('visitor', $enc->decrypt($selector->fristQueryValue));
      break;
      case 'user':
        $memberController->permission('user', $enc->decrypt($selector->fristQueryValue));
      break;
      case 'rewriter':
        $memberController->permission('rewriter', $enc->decrypt($selector->fristQueryValue));
      break;
      case 'admin':
        $memberController->permission('admin', $enc->decrypt($selector->fristQueryValue));
      break;
      case 'delete':
        $memberController->delete($enc->decrypt($selector->fristQueryValue));
      break;
    }
  }
@endphp