<h2>Add Reminder</h2>
<form id="form-add-reminder">
    <div class="row">
        <div class="col-xl-6 col-lg-12 pb-4">
            <div class="row">
                <div class="col-12">
                    <label>Vehicle Name & Plate Number</label>
                    <select class="form-select" name="vehicle_holder" id="vehicle_holder" aria-label="Default select example">
                        <option>-</option>
                    </select>
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
            <label>Reminder Context</label>
            <select class="form-select" name="reminder_context" id="reminder_context_holder" aria-label="Default select example">
                <option>-</option>
            </select>
            <label>Reminder Title</label>
            <input class="form-control" name="reminder_title" id="reminder_title">
            <label>Reminder Body</label>
            <textarea class="form-control" name="reminder_body" id="reminder_body"></textarea>
            <a class="btn btn-success rounded-pill py-3 w-100 mt-3" id="submit-add-reminder-btn"><i class="fa-solid fa-floppy-disk"></i> Save Reminder</a>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).on('click','#submit-add-reminder-btn', function(){
        post_reminder()
    })
    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        get_vehicle_detail(id)
        get_vehicle_attached_reminder(id)
    })

    const get_vehicle_name_opt = () => {
        Swal.showLoading()
        const ctx = 'vehicle_name_temp'
        const ctx_holder = 'vehicle_holder'

        const generate_vehicle_list = (holder,data) => {
            data.forEach(el => {
                $(`#${holder}`).append(`<option value="${el.id}">${el.vehicle_plate_number} - ${el.vehicle_name}</option>`)
            });
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/vehicle/name`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    localStorage.setItem(ctx,JSON.stringify(data))
                    localStorage.setItem(`last-hit-${ctx}`,Date.now())
                    generate_vehicle_list(ctx_holder,data)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 404){
                        failedMsg(`get the vehicle list`)
                    } else {
                        // .....
                    }
                }
            });
        }

        if(ctx in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < statsFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx))
                if(data){
                    generate_vehicle_list(ctx_holder,data)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg(`get the vehicle list`)
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }

    const get_context_opt = (context) => {
        Swal.showLoading()
        const ctx = `${context}_temp`
        const ctx_holder = `${context}_holder`

        const generate_context_list = (holder,data) => {
            $(`#${holder}`).empty().append(`<option>-</option>`)
            data.forEach(el => {
                $(`#${holder}`).append(`<option value="${el.dictionary_name}">${el.dictionary_name}</option>`)
            });
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/dictionary/type/${context}`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    localStorage.setItem(ctx,JSON.stringify(data))
                    localStorage.setItem(`last-hit-${ctx}`,Date.now())
                    generate_context_list(ctx_holder.includes('reminder_type') ? 'reminder_type_holder' : ctx_holder,data)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 404){
                        failedMsg(`get the ${context} list`)
                    } else {
                        // .....
                    }
                }
            });
        }

        if(ctx in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < statsFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx))
                if(data){
                    generate_context_list(ctx_holder.includes('reminder_type') ? 'reminder_type_holder' : ctx_holder,data)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg(`get the ${context} list`)
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }

    get_vehicle_name_opt()
    get_context_opt('reminder_context')

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
        const reminder_context = $('#reminder_context_holder').val()

        if(vehicle_id !== "-" && reminder_context !== "-"){
            Swal.showLoading();
            $.ajax({
                url: `/api/v1/reminder`,
                type: 'POST',
                contentType: "application/json",
                data: JSON.stringify({
                    vehicle_id: vehicle_id,
                    reminder_title: $('#vehicle_title').val(),
                    reminder_context: $('#vehicle_context').val(),
                    reminder_body: $("#reminder_body").val(),
                    remind_at: $("#remind_at").val(),
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
                    if(response.status != 400){
                        failedMsg('create reminder')
                    } else {
                        // ....
                    }
                }
            });
        } else {
            failedMsg('create reminder : you must select an item')
        }
    }
</script>