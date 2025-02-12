<div class="col-lg-6 col-md-6 col-sm-12">
    <div class="input-group mb-3">
        <input class="form-control" type="text" name="username" id="username" required>
        <span class="input-group-text">Username</span>
    </div>
    <div class="input-group mb-3">
        <input class="form-control" type="text" name="email" id="email" required>
        <span class="input-group-text">Email</span>
    </div>
    <div class="input-group mb-3">
        <input class="form-control" type="text" name="telegram_user_id" id="telegram_user_id" required>
        <span class="input-group-text">Telegram ID</span>
    </div>
    <a>Joined since :<span id="created_at"></span></a>
    <a>Updated at :<span id="updated_at"></span></a>
</div>

<script>
    const get_profile = () => {
        Swal.showLoading()
        $.ajax({
            url: `/api/v1/user/my_profile`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")    
            },
            success: function(response) {
                Swal.close()
                const data = response.data

                $(`#username`).val(data.username)
                $(`#email`).val(data.email)
                $(`#telegram_user_id`).val(data.telegram_user_id)
                $(`created_at`).val(data.created_at)
                $(`#updated_at`).val(data.updated_at)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                Swal.fire({
                    title: "Oops!",
                    text: "Something went wrong",
                    icon: "error"
                });
            }
        });
    }
    get_profile()
</script>