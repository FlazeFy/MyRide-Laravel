<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <h2 class="navbar-brand mb-0"><a href="/">MyRide</a></h2>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        @if(session()->get('token_key'))
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="btn btn-primary me-3" href="<?= Route::current()->uri() === "/" ? "/dashboard" : "/profile"?>"><i class="fa-solid fa-user"></i> {{session()->get('username_key')}}</a></li>
                <li class="nav-item"><a class="btn btn-success px-3 me-3" href="#"><i class="fa-solid fa-bell fa-lg"></i></a></li>
                <li class="nav-item"><a class="btn btn-danger px-3" data-bs-target="#modalSignOut" data-bs-toggle="modal"><i class="fa-solid fa-right-from-bracket fa-lg"></i></a></li>
            </ul>
        </div>
        @endif
    </div>
</nav>

<div class="modal fade" id="modalSignOut" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bold" id="exampleModalLabel">Warning</h4>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form action="/sign_out" method="POST" id="form-sign-out">
                    @csrf
                    <p>Are you sure want to leave this account?</p>
                    <a class="btn btn-danger mt-4" onclick="sign_out()">Yes, Sign Out</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const sign_out = () => {
        $.ajax({
            url: "/api/v1/logout",
            type: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer <?= session()->get("token_key"); ?>"
            },
            success: function(data, textStatus, jqXHR) {
                sessionStorage.clear()
                localStorage.clear()
                $('#form-sign-out').submit()
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 401) {
                    sessionStorage.clear()
                    localStorage.clear()
                    window.location.href = "/login"
                }
            }
        });
    }
</script>