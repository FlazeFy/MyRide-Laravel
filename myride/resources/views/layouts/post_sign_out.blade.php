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
                "Authorization": `Bearer ${token}`
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