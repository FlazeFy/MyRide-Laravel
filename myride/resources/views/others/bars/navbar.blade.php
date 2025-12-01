<nav class="navbar navbar-expand-lg">
    <div class="d-inline-block w-100 d-flex flex-wrap gap-3 align-items-center">
        <div class="d-inline-block">
            <button class="toogle_nav-button btn btn-primary px-3 close"><i class="fa-solid fa-bars"></i></button>
        </div>
        <div>
            <h5 class="navbar-brand"><a href="/">
                @php
                    $path = request()->path();
                    
                    if ($path === '/' || $path === '') {
                        echo "MyRide";
                    } else {
                        $segments = explode('/', trim($path, '/'));
                        $titleParts = ["MyRide"];

                        foreach ($segments as $segment) {
                            // Skip UUID
                            if (preg_match('/^[0-9a-fA-F-]{36}$/', $segment)) {
                                continue;
                            }

                            $clean = str_replace(['-', '_'], ' ', $segment);
                            $titleParts[] = ucwords($clean);
                        }

                        echo implode(' > ', $titleParts);
                    }
                @endphp
            </a></h5>
            <div class="navbar-collapse-mobile">
                <a class="btn btn-primary px-3 me-2" href="<?= Route::current()->uri() === "/" ? "/dashboard" : "/profile"?>"><i class="fa-solid fa-user"></i><span class="button-text"> {{session()->get('username_key')}}</span></a>
                <a class="btn btn-success px-3 me-2" href="#"><i class="fa-solid fa-bell fa-lg"></i></a>
                <a class="btn btn-danger px-3" data-bs-target="#modalSignOut" data-bs-toggle="modal"><i class="fa-solid fa-right-from-bracket fa-lg"></i></a>
            </div>
        </div>
        
        @if(session()->get('token_key'))
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="btn btn-primary px-3 me-2" href="<?= Route::current()->uri() === "/" ? "/dashboard" : "/profile"?>"><i class="fa-solid fa-user"></i><span class="button-text"> {{session()->get('username_key')}}</span></a></li>
                <li class="nav-item"><a class="btn btn-success px-3 me-2" href="#"><i class="fa-solid fa-bell fa-lg"></i></a></li>
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