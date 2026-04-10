<style>
    .container-chat {
        background: var(--warningColor);
        position: fixed;
        bottom: 20px; 
        right: 20px;
        padding: var(--spaceMD);
        width: 70px;
        z-index: 999;
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        border: 3px solid black;
        transition: all 0.3s ease;
    }
    .container-chat.open {
        width: 500px !important;
        background: var(--whiteColor);
    }

    #chat-holder {
        height: 450px;
        margin-top: var(--spaceXSM);
        padding: var(--spaceMD) 0;
        display: flex;
        flex-direction: column;
        overflow-y: auto; 
        scroll-behavior: smooth;
    }
    .container-chat, #chat-holder, .form-control {
        border-radius: var(--roundedMD);
    }
    .bubble {
        width: 350px;
        margin-bottom: var(--spaceSM);
        padding: var(--spaceSM);
    }
    .bubble:last-child { 
        margin-bottom: 0; 
    }
    .bubble b {
        font-size: var(--textXSM);
    }
    .bubble p {
        font-size: var(--textXXSM);
    }
    .bubble-bot {
        width: 100%;
    }
    .bubble-me {
        text-align: end;
        border-radius: calc(var(--roundedMD)*1.5) calc(var(--roundedMD)*1.5) 0 calc(var(--roundedMDs)*1.5);
        background: var(--shadowColor);
        align-self: flex-end;
    }
    .text-date {
        font-size: var(--textMini) !important;
        margin-bottom: 0;
        font-style: italic;
    }
</style>

<div class="container container-chat">
    <button class="border-0 bg-transparent w-100" id="open-chat">
        <div class="d-flex justify-content-between align-items-center">
            <div id="chat_box-title"></div>
            <div class="d-flex flex-wrap mx-auto">
                <div id="icon-holder"><i class="fa-regular fa-comments"></i></div>
            </div>
        </div>
    </button>
    <div class="collapse" id="collapseChatBox">
        <div id="chat-holder">
            <img src="{{asset('assets/chat.png')}}" alt='chat.png' class="img img-fluid w-100 mb-3 mx-auto mt-5" style="max-width: 180px;">
            <p class="font-italic text-center mx-5">Hi! I'm <b>Mira</b>, your MyRide assistant. What would you like to know?</p>
        </div>
        <div class="d-flex mt-3 align-items-end">
            <textarea class="form-control me-2 mb-0" id="question-input" style="min-height: 100px;" placeholder="Ask something..." onkeydown="return submitOnEnter(event)"></textarea>
            <a class="btn btn-success rounded-circle" id="send-chat-button"><i class="fa-solid fa-paper-plane"></i></a>
        </div>
    </div>
</div>
<input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

<script>
    const chatKey = "chatMessages"

    const submitOnEnter = (event) => {
        if (event.key === "Enter" && !event.shiftKey && !event.altKey && !event.ctrlKey && !event.metaKey) {
            event.preventDefault()
            postChat()
            return false
        }
        return true
    }

    $(document).on('click','#send-chat-button', function(){
        postChat()
    })    

    $('#collapseChatBox').on('shown.bs.collapse', function(){ 
        $('.container-chat').addClass('open')
        $('#icon-holder')
            .html('<a class="btn btn-danger rounded-pill m-0" id="hide-chat"><i class="fa-solid fa-chevron-down"></i> Hide</a>')
            .before('<a class="btn btn-danger rounded-pill m-0 me-2 start-new-chat" title="Start New Chat"><i class="fa-solid fa-trash"></i></a>')
            .closest('.d-flex')
            .removeClass('mx-auto')
        $('#chat_box-title').html('<h6 class="mb-0">Ask Me</h6>')
        $('#chat-holder').scrollTop($('#chat-holder')[0].scrollHeight)
    }) 

    $('#collapseChatBox').on('hidden.bs.collapse', function(){ 
        $('.container-chat').removeClass('open')
        $('#icon-holder')
            .html('<i class="fa-regular fa-comments"></i>')
            .closest('.d-flex')
            .addClass('mx-auto')
        $('.start-new-chat').remove()
        $('#chat_box-title').empty() 
    })

    $(document).on('click', '#open-chat', function(){
        $('#collapseChatBox').collapse('show')
    })

    $(document).on('click', '#hide-chat', function(){
        $('#collapseChatBox').collapse('hide')
    })

    $(document).on('click', '.start-new-chat', function(){
        Swal.fire({
            title: "Warning!",
            text: "Do you want to reset and start a new chat?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, start new chat",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                resetChat()
            } 
        })
    })

    const resetChat = () => {
        localStorage.removeItem(chatKey)
        $('#chat-holder').html('<p class="font-italic text-center no-msg-chat">- No message found -</p>')
    }
 
    const formatDate = (isoString) => {
        const date = new Date(isoString)
        const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]

        const day = String(date.getDate()).padStart(2, '0')
        const month = months[date.getMonth()]
        const year = date.getFullYear()
        const hours = String(date.getHours()).padStart(2, '0')
        const minutes = String(date.getMinutes()).padStart(2, '0')

        return `${day} ${month} ${year} ${hours}:${minutes}`
    }

    $(document).ready(function() {
        const messages = fetchLocalMessage()
        renderLocalMessage(messages)
    })

    const fetchLocalMessage = () => {
        if (localStorage.getItem(chatKey)) {
            const messages = JSON.parse(localStorage.getItem(chatKey))
            return messages
        }
    }

    const renderLocalMessage = (messages) => {
        if (messages){
            $('.no-msg-chat').remove()
            messages.forEach(dt => {
                $('#chat-holder').append(`
                    <div class="bubble bubble-${dt.source !== 'me' ? 'bot' : dt.source}">
                        ${dt.source === 'Mira' ? '<b>Mira</b>' :''}
                        <p class="mb-0 mt-1">${dt.message}</p>
                        <p class="text-date">${formatDate(dt.timestamp)}</p>
                    </div>
                `)
            })
        } 
    }
    
    const storeLocalMessage = (source, message) => {
        let messages = []
        if (localStorage.getItem(chatKey)) {
            messages = JSON.parse(localStorage.getItem(chatKey))
        }

        messages.push({
            source: source,
            message: message,
            timestamp: new Date().toISOString()
        })

        localStorage.setItem(chatKey, JSON.stringify(messages))
    }

    const postChat = () => {
        $.ajax({
            url: `/api/v1/chat/ai`,
            type: "POST",
            contentType: "application/json", 
            data: JSON.stringify({ question: $("#question-input").val() }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("X-CSRF-TOKEN", $("#csrf-token").val())
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                Swal.showLoading()
            },
            success: function (response) {
                Swal.close()
                const message = response.message
                const myMessage = $("#question-input").val()
                const datetime = formatDate(new Date().toISOString())
                $('.no-msg-chat').remove()
                $("#question-input").val("")

                $('#chat-holder').append(`
                    <div class="bubble bubble-me">
                        <p class="mb-0 mt-1">${myMessage}</p>
                        <p class="text-date">${datetime}</p>
                    </div>
                `)
                storeLocalMessage('me', myMessage)

                $('#chat-holder').append(`
                    <div class="bubble bubble-bot">
                        <b>Mira</b>
                        <p class="mb-0 mt-1">${message}</p>
                        <p class="text-date">${datetime}</p>
                    </div>
                `)
                storeLocalMessage('Mira', message)

                $('#chat-holder').scrollTop($('#chat-holder')[0].scrollHeight)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                
            },
        })
    }
</script>