<script>
    let page_reminder = 1

    $(document).on('click','.open-notification-btn', function(){
        $(document).ready(function() {
            const get_reminder_history = (page) => {
                ['1','2'].forEach(el => {
                    const item_holder = 'reminder-holder-2'

                    $.ajax({
                        url: `/api/v1/reminder?page=${page}`,
                        type: 'GET',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader("Accept", "application/json")
                            xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
                        },
                        success: function(response) {
                            const data = response.data.data
                            
                            $(`#${item_holder}`).empty()
                            data.forEach(el => {
                                $(`#${item_holder}`).append(`
                                    <button class='btn text-start mt-0 bordered' title='See Reminder' onclick="window.location.href='/reminder/${el.id}'">
                                    <div class="text-dark">
                                        <h6>${el.reminder_title}</h6><hr>
                                        <p class="mb-0 fw-normal">${el.reminder_body}</p> 
                                        <p class='date-text m-0 text-italic' style='font-size:var(--textMD);'>Received At : ${getDateToContext(el.created_at,'calendar')}</p>
                                        </div>
                                    </button>
                                `)
                            });
                        },
                        error: function(response, jqXHR, textStatus, errorThrown) {
                            if(response.status != 404){
                                generate_api_error(response, true)
                            } else {
                                template_alert_container(item_holder, 'no-data', "No notification to show", null, '<i class="fa-solid fa-rotate-left"></i>')
                            }
                        }
                    });
                });
            }
            get_reminder_history(page_reminder) 
        });
    })
</script>