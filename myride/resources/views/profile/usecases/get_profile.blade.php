<h2>My Profile</h2>
<label>Username</label>
<input class="form-control" type="text" name="username" id="username" required>
<label>Email</label>
<input class="form-control" type="text" name="email" id="email" required>
<label>Telegram ID</label>
<div class="input-group mb-3" id="telegram_group_id">
    <input class="form-control" type="text" name="telegram_user_id" id="telegram_user_id" required>
    <span class="input-group-text" id="telegram_validated_status"></span>
</div>
<p class="text-white mb-0">Joined since : <span id="created_at"></span></p>
<p class="text-white mb-0">Updated at : <span id="updated_at"></span></p>
<a class="btn btn-success my-2" id="save_changes_profile"><i class="fa-solid fa-floppy-disk"></i> Save Changes</a>

<script>
    let current_telegram_id
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
                const data_telegram = response.telegram_data

                $(`#username`).val(data.username)
                $(`#email`).val(data.email)
                $(`#telegram_user_id`).val(data.telegram_user_id)
                $(`#created_at`).text(data.created_at ?? '-')
                $(`#updated_at`).text(data.updated_at ?? '-')
                $('#telegram_validated_status').html(data.telegram_is_valid == 0 ? `<span class='text-danger'><i class="fa-solid fa-triangle-exclamation text-danger"></i> Not Validated</span>`:`<span class='text-success'><i class="fa-solid fa-check text-success"></i> Validated</span>`)
                current_telegram_id = data.telegram_user_id ?? ''
                if(data.telegram_is_valid == 0 && data_telegram == null){
                    $('#telegram_group_id').append(`<a class="btn btn-primary" id="request_validation_token"><i class="fa-solid fa-paper-plane"></i> Send Validation</a>`)
                }

                telegram_req_holder(data_telegram)
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

    $(document).on('click','#request_validation_token', function(){
        $.ajax({
            url: '/api/v1/user/update_telegram_id',
            type: 'PUT',
            data: {
                telegram_user_id: $("#telegram_user_id").val()
            },
            dataType: 'json',
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>");    
            },
            success: function(response) {
                Swal.close()
                Swal.fire({
                    title: "Success!",
                    text: response.message,
                    icon: "success"
                }).then((result) => {
                    if (result.isConfirmed) {
                        get_my_profile()
                    }
                });
            },
            error: function(response, textStatus, errorThrown) {
                Swal.close()
                generate_api_error(response, true)
            }
        });
    })

    $(document).on('input','#telegram_user_id', function(){
        $('#telegram_validated_status').html(current_telegram_id !== $(this).val() ? `<span class='text-danger'><i class="fa-solid fa-triangle-exclamation text-danger"></i> Changes Detected</span>`:`<span class='text-success'><i class="fa-solid fa-check text-success"></i> Validated</span>`)
    })

    $(document).on('click','#save_changes_profile', function(){
        $.ajax({
            url: '/api/v1/user/update_profile',
            type: 'PUT',
            data: {
                username: $("#username").val(),
                email: $("#email").val(),
                telegram_user_id: $("#telegram_user_id").val()
            },
            dataType: 'json',
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>");    
            },
            success: function(response) {
                Swal.close()
                Swal.fire({
                    title: "Success!",
                    text: response.message,
                    icon: "success"
                }).then((result) => {
                    if (result.isConfirmed) {
                        get_my_profile()
                    }
                });
            },
            error: function(response, textStatus, errorThrown) {
                Swal.close()
                generate_api_error(response, true)
            }
        });
    })
</script>