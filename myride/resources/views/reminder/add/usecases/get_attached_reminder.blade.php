<label>Attached Reminder</label>
<div id="reminder-holder"></div>

<script>
    const reminder_holder = 'reminder-holder'
    messageAlertBox(reminder_holder, "danger", "You must select a vehicle first")

    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        get_vehicle_attached_reminder(id)
    })

    const get_vehicle_attached_reminder = (id) => {
        if(id !== "-"){
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
                    
                    $(`#${reminder_holder}`).empty()
                    data.forEach(dt => {
                        $(`#${reminder_holder}`).append(`
                            <div class="container-fluid bg-success">
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
                        generateApiError(response, true)                
                    } else {
                        messageAlertBox(reminder_holder, "danger", "No active reminder attached to this vehicle")
                    }
                }
            });
        } else {
            messageAlertBox(reminder_holder, "danger", "You must select a vehicle first")
        }
    }
</script>