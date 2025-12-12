<form action="/login/validate" method="POST" id="form-login">
    @csrf
    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Email address / Username</label>
        <input type="text" name="username" id="username" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" onkeydown="return submitOnEnter(event)">
        <div id="emailHelp" class="form-text">We'll never share your email with anyone else</div>
        <a class="error_input" id="username_msg"></a>
    </div>
    <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" id="exampleInputPassword1" onkeydown="return submitOnEnter(event)">
        <a class="error_input" id="pass_msg"></a>
    </div>
    <a class="error_input" id="all_msg"></a>

    <input hidden name="token" value="" id="token">
    <input hidden name="email" value="" id="email">
    <input hidden name="role" value="" id="role">
    <input hidden name="profile_pic" value="" id="profile_pic">
    <a onclick="login()" class="btn btn-success w-100 mb-3"><i class="fa-solid fa-arrow-right-to-bracket mx-1"></i> Enter the Garage</a>
    <a href="/auth/google" class="btn btn-primary w-100"><i class="fa-brands fa-google mx-1"></i> Sign In With Google</a>
    <hr>
    <div class="row">
        <div class="col-md-6 col-sm-12 p-1">
            <a href="/" class="btn btn-danger w-100"><i class="fa-solid fa-arrow-left mx-1"></i> Back to Landing</a>
        </div>
        <div class="col-md-6 col-sm-12 p-1">
            <a href="/register" class="btn btn-primary w-100"><i class="fa-solid fa-arrow-right-to-bracket mx-1"></i> Register</a>
        </div>
    </div>
</form>

<script>
    const viewPassword = () => {
        if($("#password").getAttribute('type') == "text"){
            $("#password").setAttribute('type', 'password')
            $("#btn-toogle-pwd").html('<i class="fa-sharp fa-solid fa-eye-slash"></i>')
        } else {
            $("#password").setAttribute('type', 'text')
            $("#btn-toogle-pwd").html('<i class="fa-sharp fa-solid fa-eye"></i>')
        }
    }

    const login = () => {
        $('#username_msg').html("")
        $('#pass_msg').html("")
        $('#all_msg').html("")

        $.ajax({
            url: '/api/v1/login',
            type: 'POST',
            data: $('#form-login').serialize(),
            dataType: 'json',
            success: function(response) {
                var found = false

                if(response.hasOwnProperty('role')){
                    found = true
                }
                
                if(found){
                    $('#token').val(response.token)
                    $('#role').val(response.role)
                    $('#email').val(response.result.email)
                    $('#profile_pic').val(response.result.image_url)
                    $('#form-login').submit()
                } else {
                    $('#username_msg').html("")
                    $('#pass_msg').html("")
                    $('#all_msg').html("")

                    $('#text-sorry').text("Sorry, something is wrong")
                    $('#sorry_modal').modal('show')
                }
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                var errorMessage = "Unknown error occurred"
                var usernameMsg = null
                var passMsg = null
                var allMsg = null
                var icon = `<i class='fa-solid fa-triangle-exclamation'></i> `

                if (response && response.responseJSON && response.responseJSON.hasOwnProperty('result')) {   
                    //Error validation
                    if(typeof response.responseJSON.result === "string"){
                        allMsg = response.responseJSON.result
                    } else {
                        if(response.responseJSON.result.hasOwnProperty('username')){
                            usernameMsg = response.responseJSON.result.username[0]
                        }
                        if(response.responseJSON.result.hasOwnProperty('password')){
                            passMsg = response.responseJSON.result.password[0]
                        }
                    }
                    
                } else if(response && response.responseJSON && response.responseJSON.hasOwnProperty('errors')){
                    allMsg = response.responseJSON.errors.result[0]
                } else {
                    allMsg = errorMessage
                }

                //Set to html
                if(usernameMsg){
                    $('#username_msg').html(icon + usernameMsg)
                }
                if(passMsg){
                    $('#pass_msg').html(icon + passMsg)
                }
                if(allMsg){
                    $('#all_msg').html(icon + allMsg)
                }
            }
        });
    }

    const auto_login = () => {
        if(localStorage.getItem('token_key') !== null){
            const token = localStorage.getItem('token_key')
            Swal.showLoading()
            $.ajax({
                url: `/api/v1/user/my_profile`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    $('#token').val(token)
                    $('#role').val(data.role)
                    $('#email').val(data.email)
                    $('#id').val(data.id)
                    is_submit = true
                    $('#form-login').submit()
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    sessionStorage.clear()
                    localStorage.clear()
                    generate_api_error(response, true)
                }
            });
        } else {
            sessionStorage.clear()
            localStorage.clear()
        }
    }
    auto_login()

    const submitOnEnter = (event) => {
        if (event.keyCode === 13) { 
            event.preventDefault() 
            login()
            return false 
        }
        return true 
    }
</script>