<div class="text-center mt-3 section-form">
    <h3>Validate</h3><br>
    <h4 class="my-2 fw-bold" style="font-size:var(--textJumbo);" id="timer">15:00</h4>
    <label>Type the Token that has sended to your email</label><br>
    <div class="pin-code" id="pin-holder">
        <input type="text" maxlength="1" oninput="validatePin()" autofocus>
        <input type="text" maxlength="1" oninput="validatePin()">
        <input type="text" maxlength="1" oninput="validatePin()">
        <input type="text" maxlength="1" oninput="validatePin()">
        <input type="text" maxlength="1" oninput="validatePin()">
        <input type="text" maxlength="1" oninput="validatePin()">
    </div>
    <div id="token_validate_msg" class="msg-error-input mb-2" style="font-size:13px;"></div>
    <div class="d-inline-block mx-auto">
        <a class="btn btn-success rounded-pill px-3 mt-3" id="btn-regenerate-token">Don't receive the token. Send again!</a>
    </div>
</div>

<script>
    let pinContainer = document.querySelector(".pin-code")
    let pin_holder = document.getElementById('pin-holder')
    let timer = document.getElementById("timer")
    let remain = 900

    pinContainer.addEventListener('keyup', function (event) {
        var target = event.srcElement
        
        var maxLength = parseInt(target.attributes["maxlength"].value, 10)
        var myLength = target.value.length

        if (myLength >= maxLength) {
            var next = target
            while (next = next.nextElementSibling) {
                if (next == null) break
                if (next.tagName.toLowerCase() == "input") {
                    next.focus()
                    break
                }
            }
        }

        if (myLength === 0) {
            var next = target;
            while (next = next.previousElementSibling) {
                if (next == null) break
                if (next.tagName.toLowerCase() == "input") {
                    next.focus()
                    break
                }
            }
        }
    }, false);

    function formatTime(seconds){
        var minutes = Math.floor(seconds / 60);
        var remainingSeconds = seconds % 60;
        return minutes + ':' + remainingSeconds.toString().padStart(2, '0');
    }

    function controlPin(type) {
        var pins = pin_holder.querySelectorAll('input');
        var result = "";

        pins.forEach(function(e) {
            if(type == "time_out"){
                e.disabled = true
                e.style = "background: var(--hoverBG);"
            } else if(type == "regenerate"){
                e.disabled = false
                e.value = ""
                e.style = "background: var(--firstColor);"
            } else if(type == "invalid"){
                e.value = ""
                e.style = "border: 1.5px solid var(--warningBG); "
            } else if(type == "fetch"){
                result += e.value
            }
        });

        return result;
    }

    function validatePin(){
        var pins = pin_holder.querySelectorAll('input')
        var is_empty = false

        pins.forEach(function(e) {
            if(e.value == "" || e.value == null){
                is_empty = true
                return
            }
        });

        if(is_empty == false){
            const token = controlPin('fetch')
            validateToken(token)
        }
    }

    function validateToken(token){
        $.ajax({
            url: `/api/v1/register/account`,
            dataType: 'json',
            contentType: 'application/json',
            type: "POST",
            data: JSON.stringify({
                username: $('#username').val(),
                email: $('#email').val(),
                password: $('#password').val(),
                token: token
            }),
            beforeSend: function (xhr) {
                Swal.showLoading()
            }
        })
        .done(function (response) {            
            const data = response
            Swal.hideLoading()

            localStorage.setItem('token_key',response.token)

            Swal.fire({
                title: "Success!",
                text: data.message,
                icon: "success",
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.step .title').text("Stay Updated!")
                    $('.step .caption').text('Sync your Telegram with our app to receive instant updates, alerts, and messages without missing a single update.')
                    $('.progress-bar').css('width', '66%').attr('aria-valuenow', 66) 
                    $('.step .caption').after(`
                        <hr>
                        <p>You have finished your registration process. You can skip this part if you wanted</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-success px-2 py-1" href='/dashboard'>
                                <i class="fa-solid fa-arrow-right"></i> Dashboard
                            </a>
                            <a class="btn btn-primary px-2 py-1">
                                <i class="fa-solid fa-mobile-screen"></i> Get Mobile Version
                            </a>
                        </div>
                    `)
                    $('#token-section').addClass('d-none').removeClass('d-block')
                    $('#service-section').addClass('d-block').removeClass('d-none')
                    $('#back-button').html('<i class="fa-solid fa-house mx-1"></i> Login').attr('data-current-step', 'service').attr('href','/login')
                }
            });
        })
        .fail(function (response, xhr, ajaxOptions, thrownError) {
            generate_api_error(response, true)
            var pins = pin_holder.querySelectorAll('input')
            var is_empty = false

            pins.forEach(function(e, index) {
                e.value = ""
                if (index === 0) {
                    e.focus()
                }
            });
        })
    }
    
    function startTimer(duration) {
        var remain = duration

        function updateTimer() {
            timer.innerHTML = formatTime(remain)

            if (remain > 0) {
                remain--
                setTimeout(updateTimer, 1000)

                if (remain <= 180) {
                    timer.style = "color: var(--warningBG);"
                }
            } else {
                token_msg.innerHTML = "<span class='text-danger'>Time's up, please try again</span>"
                controlPin("time_out")
            }
        }

        updateTimer()
    }

    pinContainer.addEventListener('keydown', function (event) {
        var target = event.srcElement
        target.value = ""
    }, false);
</script>