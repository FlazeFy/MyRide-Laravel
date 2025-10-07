<h2>Add Driver</h2>
<form id="form-add-driver">
    <div class="row">
        <div class="col-xl-6 col-lg-12 pb-4">
            <div class="row">
                <div class="col-12">
                    <label>Fullname</label>
                    <input class="form-control" name="fullname" id="fullname">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Username</label>
                    <input class="form-control" name="username" id="username">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Email</label>
                    <input class="form-control" name="email" id="email" type="email">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Telegram ID</label>
                    <input class="form-control" name="telegram_user_id" id="telegram_user_id">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Phone</label>
                    <input class="form-control" name="phone" id="phone">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Password</label>
                    <input class="form-control" name="password" id="password" type="password">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Password Confirmation</label>
                    <input class="form-control" name="password_confirmation" id="password_confirmation" type="password">
                </div>
            </div>
            <a class="btn btn-success rounded-pill py-3 w-100 mt-3" id="submit-add-driver-btn"><i class="fa-solid fa-floppy-disk"></i> Save Driver</a>
        </div>
        <div class="col-xl-6 col-lg-12">
            @include('driver.add.usecases.get_list_assigned_driver')
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).on('click','#submit-add-driver-btn', function(){
        post_driver()
    })

    const post_driver = () => {
        const vehicle_id = $('#vehicle_holder').val()
        const driver_category = $('#driver_category_holder').val()

        if(vehicle_id !== "-" && driver_category !== "-"){
            Swal.showLoading();
            $.ajax({
                url: `/api/v1/driver`,
                type: 'POST',
                contentType: "application/json",
                data: JSON.stringify({
                    username:$('#username').val(),
                    fullname:$('#fullname').val(),
                    password:$('#password').val(),
                    password_confirmation:$('#password_confirmation').val(),
                    email:$('#email').val(),
                    phone:$('#phone').val(),
                    telegram_user_id:$('#telegram_user_id').val(),
                }),
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
                },
                success: function(response) {
                    Swal.close()
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success"
                    }).then(() => {
                        window.location.href = '/driver'
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status === 500){
                        failedMsg('create driver')
                    } else {
                        failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                    }
                }
            });
        } else {
            failedMsg('create driver : you must select an item')
        }
    }
</script>