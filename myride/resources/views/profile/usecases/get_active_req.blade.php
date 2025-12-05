<span id="req_holder"></span>

<script>
    const telegram_req_holder = (data) => {
        if(data){
            $('#req_holder').html(`
                <div class="alert alert-success telegram-request" role="alert">
                    <p>There is a request for <b class='text-dark'>Telegram verification</b>. We have sended the token to your Telegram, please type the token and validate it</p>
                    <div class='d-flex justify-content-start gap-2'>
                        <input class="form-control mb-0" type="text" style='color:var(--darkColor) !important;' name="telegram_request_context" id="telegram_request_context" required>
                        <a class='btn btn-success' id='validate-telegram-req-btn'>Validate</a>
                    </div>
                </div>
            `)
        }
    }

    $(document).on('click','#validate-telegram-req-btn', function(){
        $.ajax({
            url: '/api/v1/user/validate_telegram_id',
            type: 'PUT',
            data: {
                request_context: $('#telegram_request_context').val()
            },
            dataType: 'json',
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json");
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
            },
            success: function(response) {
                Swal.hideLoading()
                Swal.fire({
                    title: "Success!",
                    text: response.message,
                    icon: "success",
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        get_profile()
                        $('#req_holder .telegram-request').remove()
                    }
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                generate_api_error(response, true)
            }
        });
    })
</script>