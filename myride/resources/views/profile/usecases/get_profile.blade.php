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
<a class="btn btn-success my-2"><i class="fa-solid fa-floppy-disk"></i> Save Changes</a>

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
                const data_telegram = response.telegram_data

                $(`#username`).val(data.username)
                $(`#email`).val(data.email)
                $(`#telegram_user_id`).val(data.telegram_user_id)
                $(`#created_at`).text(data.created_at ?? '=')
                $(`#updated_at`).text(data.updated_at ?? '-')
                $('#telegram_validated_status').html(data.telegram_is_valid == 0 ? `<span class='text-danger'><i class="fa-solid fa-check text-danger"></i> Not Validated</span>`:`<span class='text-success'><i class="fa-solid fa-xmark text-success"></i> Validated/span>`)
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
                var errorMessage = "Unknown error occurred"
                var allMsg
                var icon = `<i class='fa-solid fa-triangle-exclamation'></i> `

                if (response.responseJSON && response.responseJSON.hasOwnProperty('message')) {
                    allMsg = response.responseJSON.message
                } else if (response.responseJSON && response.responseJSON.hasOwnProperty('result')) {
                    if (typeof response.responseJSON.result === "string") {
                        allMsg = response.responseJSON.result
                    } 
                } else if (response.responseJSON && response.responseJSON.hasOwnProperty('errors')) {
                    allMsg = response.responseJSON.errors.result[0]
                } else {
                    allMsg = errorMessage
                }

                if (allMsg) {
                    $('#all_msg').html(icon + allMsg);
                    Swal.fire({
                        title: "Oops!",
                        text: allMsg,
                        icon: "error"
                    });
                }
            }
        });
    })
</script>