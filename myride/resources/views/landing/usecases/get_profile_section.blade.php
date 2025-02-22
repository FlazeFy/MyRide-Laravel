<div>
    <div style="margin-top: 30vh;" class="text-center text-white">
        <img class="img img-fluid rounded-circle mb-2" style="height: 25vh; width:25vh;" src="{{ asset('/assets/user_default.jpg') }}">
        <h3>{{session()->get('username_key')}}</h3>
    </div>
    <div class="p-3 my-2 w-100 d-flex justify-content-center">
        <a class="btn btn-success rounded-circle p-2 m-2 pt-3" href="/profile" style="height: 60px; width: 60px;"><i class="fa-solid fa-user fa-lg"></i></a>
        <a class="btn btn-danger rounded-circle p-2 m-2 pt-3" data-bs-target="#modalSignOut" data-bs-toggle="modal" style="height: 60px; width: 60px;"><i class="fa-solid fa-right-from-bracket fa-lg"></i></a>
    </div>
</div>