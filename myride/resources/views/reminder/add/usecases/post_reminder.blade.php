<h2>Add Reminder</h2>
<form id="form-add-reminder">
    <div class="row">
        <div class="col-xl-6 col-lg-12 pb-4">
            <div class="row">
                <div class="col-12">
                    <label>Vehicle Name & Plate Number</label>
                    <select class="form-select" name="vehicle_holder" id="vehicle_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Type</label>
                    <input class="form-control" name="vehicle_type" id="vehicle_type" readonly>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Category</label>
                    <input class="form-control" name="vehicle_category" id="vehicle_category" readonly>
                </div>
            </div>
            <hr>
            <label>Attached Reminder</label>
            <div id="attached_reminder-holder"></div>
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <label>Reminder Context</label>
                    <select class="form-select" name="reminder_context" id="reminder_context_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-md-6 col-sm-12">
                    <label>Remind At</label>
                    <input class="form-control" type="datetime-local" name="remind_at" id="remind_at">
                </div>
            </div>
            <label>Reminder Title</label>
            <input class="form-control" name="reminder_title" id="reminder_title">
            <label>Reminder Body</label>
            <textarea class="form-control" name="reminder_body" id="reminder_body"></textarea>
            <a class="btn btn-success rounded-pill py-3 w-100 mt-3" id="submit-add-reminder-btn"><i class="fa-solid fa-floppy-disk"></i> Save Reminder</a>
        </div>
    </div>
</form>

<script type="text/javascript">
    const token = `<?= session()->get("token_key"); ?>`

    $(document).on('click','#submit-add-reminder-btn', function(){
        post_reminder()
    })
    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        get_vehicle_detail(id)
        get_vehicle_attached_reminder(id)
    })

    get_vehicle_name_opt(token)
    get_context_opt('reminder_context',token)

    const get_vehicle_detail = (id) => {
        Swal.showLoading();
        $.ajax({
            url: `/api/v1/vehicle/detail/${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                $('#vehicle_type').val(data.vehicle_type)
                $('#vehicle_category').val(data.vehicle_category)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                failedMsg('get the vehicle')
            }
        });
    }

    const get_vehicle_attached_reminder = (id) => {
        const holder = 'attached_reminder-holder'
        Swal.showLoading();
        $.ajax({
            url: `/api/v1/reminder/vehicle/${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                
                data.forEach(dt => {
                    $(`#${holder}`).append(`
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
                if(response.status != 404){
                    failedMsg('get the vehicle last reminder')
                } else {
                    $(`#${holder}`).html(`
                        <div class="container bg-danger">
                            <h6><i class="fa-solid fa-triangle-exclamation"></i> Alert</h6>
                            <p class="mb-0">No active reminder attached to this vehicle</p>
                        </div>
                    `)
                }
            }
        });
    }

    const post_reminder = () => {
        const vehicle_id = $('#vehicle_holder').val()
        const reminder_context = $('#reminder_context_holder').val()

        if(vehicle_id !== "-" && reminder_context !== "-"){
            Swal.showLoading();
            $.ajax({
                url: `/api/v1/reminder`,
                type: 'POST',
                contentType: "application/json",
                data: JSON.stringify({
                    vehicle_id: vehicle_id,
                    reminder_title: $('#reminder_title').val(),
                    reminder_context: reminder_context,
                    reminder_body: $("#reminder_body").val(),
                    remind_at: formatDateTimeAPI($('#remind_at').val()),
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
                        window.location.href = '/reminder'
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status === 500){
                        failedMsg('create reminder')
                    } else {
                        failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                    }
                }
            });
        } else {
            failedMsg('create reminder : you must select an item')
        }
    }
</script>