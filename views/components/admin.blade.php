@use(Mlkali\Sa\Support\Messages)
@if ($member->permission !== 'admin')
@redirect('/?message=', Messages::DANGER_USER_PERMISSION)
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
        @foreach ($memberController->member() as $key => $data)
        <tr>
            <th scope="row">{{$data['id']}}</th>
            <td>{{$data['username']}}</td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false">
                        <span>{{$data['permission']}}</span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a href="/admin?id={{$enc->encrypt($data['member_id'])}}&action={{$enc->encrypt('visitor')}}" class="dropdown-item">visitor</a>
                        <a href="/admin?id={{$enc->encrypt($data['member_id'])}}&action={{$enc->encrypt('user')}}" class="dropdown-item">user</a>
                        <a href="/admin?id={{$enc->encrypt($data['member_id'])}}&action={{$enc->encrypt('rewriter')}}" class="dropdown-item">rewriter</a>
                        <a href="/admin?id={{$enc->encrypt($data['member_id'])}}&action={{$enc->encrypt('admin')}}" class="dropdown-item">admin</a>
                    </ul>
                </div>
            </td>
            <td><a class="text-danger" href="/admin?id={{$data['id']}}&action={{$enc->encrypt('delete')}}">Delete</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@php
// TODO: THIS FILE will be done last its not necessary for working website
// TODO: query message should be cleaned before use
// NOTE: this page is avaible just for Admin only
$action = $selector->getQueryMessage("action");
//NOTE - can be null if id not in query: ?action=xxx&id=ID
$memberID = $selector->getQueryMessage("id");
@endphp
@if($action)
@match($enc->decrypt($action))
    @state('visitor')
        @do($memberController->permission('visitor', $memberID))
    @state('user')
        @do($memberController->permission('user', $memberID))
    @state('rewriter')
        @do($memberController->permission('rewriter', $memberID))
    @state('admin')
        @do($memberController->permission('admin', $memberID))
    @state('delete')
        @do($memberController->delete($enc->decrypt($memberID)))
@endmatch()
@endif()
