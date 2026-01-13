<h2 class="fw-bold" style="font-size:36px;">Profile</h2>
<label>Username</label>
<input type="text" name="username" id="username" class="form-control"/>
<label>Email</label>
<input type="email" name="email" id="email" class="form-control"/>
<label>Password</label>
<input type="password" name="password" id="password" class="form-control"/>
<label>Re-Type Password</label>
<input type="password" name="password_validation" id="password_validation" class="form-control"/>
<a class="btn btn-success w-100 mt-4" id="btn-register-acc"><i class="fa-solid fa-paper-plane"></i> Register Account</a><br>

<script>
    $(document).ready(function() {
        $('#checkTerm').click(function() {
            if ($(this).is(':checked')) {

            } else {
                $('#username, #email, #password, #password_validation').val('')
            }
        });

        $('#btn-regenerate-token').on('click', function(){
            $.ajax({
                url: `/api/v1/register/regen_token`,
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({
                    username:$('#username').val(),
                    email:$('#email').val()
                }), 
                type: "POST",
                beforeSend: function (xhr) {
                    Swal.showLoading()
                }
            })
            .done(function (response) {
                startTimer(900)
                let data = response
                Swal.hideLoading()
                Swal.fire(`Token ${data.status}`, data.message, data.status)
            })
            .fail(function (response, textStatus, errorThrown) {
                generateApiError(response, true)
            });
        })

        $('#btn-register-acc').on('click', function(){
            if(validateInput('text', 'username', 36, 6) && validateInput('text', 'password', 36, 6) && validateInput('text', 'email', 255, 10)){
                if($('#password').val() == $('#password_validation').val()){
                    if($('#email').val().includes("gmail")){
                        $.ajax({
                            url: `/api/v1/register/token`,
                            dataType: 'json',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                username:$('#username').val(),
                                email:$('#email').val()
                            }), 
                            type: "POST",
                            beforeSend: function (xhr) {
                                Swal.showLoading()
                            },
                            success: function(response) {
                                Swal.close()
                                $('#checkTerm').attr('disabled', true)
                                $('#username, #email, #password, #password_validation').attr('readonly',true)
                                $('#btn-register-acc').remove()
                                $(this).attr('disabled', true)
                                startTimer(900)

                                let data = response
                                Swal.fire({
                                    title: `Token ${data.status}`,
                                    text: data.message,
                                    icon: data.status,
                                    allowOutsideClick: false,
                                    showCancelButton: false,
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $('#profile-section').addClass('d-none').removeClass('d-block')
                                        $('#token-section').addClass('d-block').removeClass('d-none')
                                        $('#back-button').html('<i class="fa-solid fa-arrow-left mx-1"></i> Profile').attr('data-current-step', 'token')
                                    }
                                });

                            },
                            error: function(response, jqXHR, textStatus, errorThrown) {
                                generateApiError(response, true)
                            }
                        })
                    } else {
                        Swal.fire("Oops!", 'Email must be at @gmail format', "error")
                    }
                } else {
                    Swal.fire("Oops!", `Your password validation is not same`, "error")
                }
            } else {
                Swal.fire("Oops!", `Some field may not valid. Check again!`, "error")
            }
        })
    });
</script>