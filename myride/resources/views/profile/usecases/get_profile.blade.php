<style>
    #telegram_validated_status {
        font-size:var(--textLG);
    }
    @media (max-width: 767px) {
        #telegram_validated_status {
            font-size:var(--textMD);
        } 
    }
</style>

<h2>My Profile</h2><hr>
<label>Username</label>
<input class="form-control" type="text" name="username" id="username" required>
<label>Email</label>
<input class="form-control" type="text" name="email" id="email" required>
<label>Telegram ID</label>
<div id="telegram_group_id" class="d-flex flex-nowrap align-items-center gap-2">
    <div class="input-group" >
        <input class="form-control" type="text" name="telegram_user_id" id="telegram_user_id" required>
        <span class="input-group-text" id="telegram_validated_status"></span>
    </div>
</div>
<div class="d-flex flex-sm-row flex-column justify-content-between mt-2">
    <p class="text-secondary mb-0">Joined since : <span id="created_at"></span></p>
    <p class="text-secondary mb-0">Updated at : <span id="updated_at"></span></p>
</div>
<div class="d-grid d-md-inline-block">
    <a class="btn btn-success my-2 w-100 w-md-auto" id="save_changes_profile"><i class="fa-solid fa-floppy-disk"></i> Save Changes</a>
</div>

<script>
    let current_telegram_id
    const get_profile = () => {
        $.ajax({
            url: `/api/v1/user/my_profile`,
            type: 'GET',
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
            },
            success: function(response) {
                Swal.hideLoading()
                const data = response.data
                const data_telegram = response.telegram_data

                $(`#username`).val(data.username)
                $(`#email`).val(data.email)
                $(`#telegram_user_id`).val(data.telegram_user_id)
                $(`#created_at`).text(data.created_at ?? '-')
                $(`#updated_at`).text(data.updated_at ?? '-')
                $('#telegram_validated_status').html(
                    (data.telegram_is_valid == 0 && data.telegram_user_id) || data.telegram_user_id === null ? `<span class='text-danger'><i class="fa-solid fa-triangle-exclamation text-danger"></i> Not ${data.telegram_user_id ? 'Validated' : 'Attached'}</span>` : `<span class='text-success'><i class="fa-solid fa-check text-success"></i> Validated</span>` 
                )
                current_telegram_id = data.telegram_user_id ?? ''
                if(data.telegram_is_valid === 0 && data_telegram == null && data.telegram_user_id){
                    $('#telegram_group_id').append(`<a class="btn btn-primary text-nowrap" id="request_validation_token"><i class="fa-solid fa-paper-plane"></i><span class="d-none d-md-inline"> Send Validation</span></a>`)
                }

                telegram_req_holder(data_telegram)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                generateApiError(response, true)
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
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
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
                generateApiError(response, true)
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
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
            },
            success: function(response) {
                Swal.close()
                Swal.fire({
                    title: "Success!",
                    text: response.message,
                    icon: "success"
                }).then((result) => {
                    if (result.isConfirmed) {
                        get_profile()
                    }
                });
            },
            error: function(response, textStatus, errorThrown) {
                generateApiError(response, true)
            }
        });
    })
</script>