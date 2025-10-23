<label>Attached Reminder</label>
<div id="reminder-holder">
    <div class="no-msg-text">- No Attached Reminder Found -</div>
</div>

<script>
    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        if(id !== "-"){
            get_vehicle_attached_reminder(id)
        } else {
            $(`#${reminder_holder}`).html(`<div class="no-msg-text">- No Attached Reminder Found -</div>`)
        }
    })

    const get_vehicle_attached_reminder = (id) => {
        Swal.showLoading()
        $.ajax({
            url: `/api/v1/reminder/vehicle/${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                
                data.forEach(dt => {
                    $(`#${reminder_holder}`).append(`
                        <div class="container bg-success">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="mb-0">${dt.reminder_title}</h5>
                                    <p class="text-secondary text-dark">Reminder at ${getDateToContext(dt.remind_at,'calendar')}</p>
                                </div>
                                <h5 class="chip bg-info">${dt.reminder_context}</h5>
                            </div>
                            <h6 class="chip bg-warning d-inline" style="font-size:var(--textXLG);">${dt.reminder_body}</h6>
                        </div>
                    `)
                })
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status !== 404){
                    failedMsg('get the vehicle last reminder')
                } else {
                    $(`#${reminder_holder}`).html(`
                        <div class="container bg-danger">
                            <h6><i class="fa-solid fa-triangle-exclamation"></i> Alert</h6>
                            <p class="mb-0">No active reminder attached to this vehicle</p>
                        </div>
                    `)
                }
            }
        });
    }
</script>