<style>
    .popover-header {
        font-size: var(--textXLG);
        background: var(--primaryColor);
        color: var(--whiteColor);
        border-radius: var(--roundedMD) var(--roundedMD) 0 0;
    }
    .wide-popover {
        min-width: 400px !important;
    }
    .box-reminder-recently {
        color: var(--darkColor);
        background: var(--whiteColor);
        border-radius: var(--roundedLG);
        text-align: start;
        padding: var(--spaceSM);
        width: 100%;
    }
    @media (max-width: 767px) {
        .wide-popover {
            min-width: calc(100% - var(--spaceLG)) !important;
            margin-inline: var(--spaceMD) !important;
        }
    }
</style>

<script>
    let page_reminder = 1

    $(document).on('click','.open-notification-btn', function(){
        $(document).ready(function() {
            const get_reminder_history = (page) => {
                ['1','2'].forEach(hd => {
                    const item_holder = `reminder-holder-${hd}`

                    $.ajax({
                        url: `/api/v1/reminder/recently?page=${page}`,
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
                                    <button class='box-reminder-recently' title='See Reminder' onclick="window.location.href='/reminder/${el.id}'">
                                        <div class="mb-2">
                                            <span class="plate-number d-inline-block mb-0 mx-0">${el.vehicle_plate_number}</span><span class="chip bg-primary ms-2">${el.reminder_context}</span>
                                        </div>
                                        <h6>${el.reminder_title}</h6>
                                        <p class="mb-0 fw-normal">${el.reminder_body}</p> 
                                        <p class='date-text m-0 mt-2 text-italic'>Received At : ${getDateToContext(el.created_at,'calendar')}</p>
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