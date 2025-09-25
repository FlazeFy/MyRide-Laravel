<h2>Add Clean</h2>
<form id="form-add-clean">
    <div class="row">
        <div class="col-xl-6 col-lg-12 pb-4">
            <div class="row">
                <div class="col-12">
                    <label>Vehicle Name & Plate Number</label>
                    <select class="form-select" name="vehicle_holder" id="vehicle_holder" aria-label="Default select example">
                        <option>-</option>
                    </select>
                </div>
                <div class="col-md-6 col-sm-12">
                    <label>Type</label>
                    <input class="form-control" name="vehicle_type" id="vehicle_type" readonly>
                </div>
                <div class="col-md-6 col-sm-12">
                    <label>Category</label>
                    <input class="form-control" name="vehicle_category" id="vehicle_category" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <label>Start At</label>
                    <input class="form-control" type="datetime-local" name="clean_start_time" id="clean_start_time">
                </div>
                <div class="col-md-6 col-sm-12">
                    <label>End At</label>
                    <input class="form-control" type="datetime-local" name="clean_end_time" id="clean_end_time">
                </div>
            </div>
            <hr>
            <label>Last Clean</label>
            <div id="last_clean-holder"></div>
        </div>
        <div class="col-xl-6 col-lg-12">
            <label>Clean By</label>
            <select class="form-select" name="clean_by" id="clean_by_holder" aria-label="Default select example">
                <option>-</option>
            </select>
            <label>Address</label>
            <textarea class="form-control" name="clean_address" id="clean_address" required></textarea>
            <label>Description</label>
            <textarea class="form-control" name="clean_desc" id="clean_desc" required></textarea>
            <label>Checklist</label>
            <div class="bordered rounded p-3">
                <div class="row" id="checklist-holder"></div>
            </div>
            <a class="btn btn-success rounded-pill py-3 w-100 mt-3" id="submit-add-clean-btn"><i class="fa-solid fa-floppy-disk"></i> Save Clean</a>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).on('click','#submit-add-clean-btn', function(){
        post_clean()
    })
    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        get_vehicle_detail(id)
        get_vehicle_last_clean(id)
    })

    const set_checklist_holder = () => {
        const list_checklist = ['is_clean_body', 'is_clean_window', 'is_clean_dashboard', 'is_clean_tires', 'is_clean_trash', 'is_clean_engine', 'is_clean_seat', 'is_clean_carpet', 'is_clean_pillows', 'is_fill_window_cleaning_water', 'is_clean_hollow']

        $('#checklist-holder').empty()
        list_checklist.forEach(dt => {
            $('#checklist-holder').append(`
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="${dt}">
                        <label class="form-check-label">${ucEachWord(dt.replaceAll('_',' ').replaceAll('is',''))}</label>
                    </div>
                </div>
            `)
        });
    }
    set_checklist_holder()

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
                    generate_context_list(ctx_holder.includes('clean_type') ? 'clean_type_holder' : ctx_holder,data)
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
                    generate_context_list(ctx_holder.includes('clean_type') ? 'clean_type_holder' : ctx_holder,data)
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
    get_context_opt('clean_by')

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

    const get_vehicle_last_clean = (id) => {
        const holder = 'last_clean-holder'
        Swal.showLoading();
        $.ajax({
            url: `/api/v1/clean/last?vehicle_id=${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                let clean_checklist = ''

                if (data.is_clean_body) {
                    clean_checklist += '<h6 class="chip bg-warning d-inline mx-0">Body</h6> '
                }
                if (data.is_clean_window) {
                    clean_checklist += '<h6 class="chip bg-warning d-inline mx-0">Window</h6> '
                }
                if (data.is_clean_dashboard) {
                    clean_checklist += '<h6 class="chip bg-warning d-inline mx-0">Dashboard</h6> '
                }
                if (data.is_clean_tires) {
                    clean_checklist += '<h6 class="chip bg-warning d-inline mx-0">Tires</h6> '
                }
                if (data.is_clean_trash) {
                    clean_checklist += '<h6 class="chip bg-warning d-inline mx-0">Trash</h6> '
                }
                if (data.is_clean_engine) {
                    clean_checklist += '<h6 class="chip bg-warning d-inline mx-0">Engine</h6> '
                }
                if (data.is_clean_seat) {
                    clean_checklist += '<h6 class="chip bg-warning d-inline mx-0">Seat</h6> '
                }
                if (data.is_clean_carpet) {
                    clean_checklist += '<h6 class="chip bg-warning d-inline mx-0">Carpet</h6> '
                }
                if (data.is_clean_pillows) {
                    clean_checklist += '<h6 class="chip bg-warning d-inline mx-0">Pillows</h6> '
                }
                if (data.is_fill_window_cleaning_water) {
                    clean_checklist += '<h6 class="chip bg-warning d-inline mx-0">Window Cleaning Water</h6> '
                }
                if (data.is_clean_hollow) {
                    clean_checklist += '<h6 class="chip bg-warning d-inline mx-0">Hollow</h6> '
                }

                clean_checklist = clean_checklist.replace(/, $/, '')
                
                $(`#${holder}`).html(`
                    <div class="container bg-success">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="mb-0">${data.clean_desc}</h5>
                                <p class="text-secondary text-dark">Clean at ${getDateToContext(data.created_at,'calendar')}</p>
                            </div>
                            <h5 class="chip bg-info">${data.clean_by}</h5>
                        </div>
                        <div>${clean_checklist}</div>
                    </div>
                `)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status != 404){
                    failedMsg('get the vehicle last clean')
                } else {
                    $(`#${holder}`).html(`
                        <div class="container bg-danger">
                            <h6><i class="fa-solid fa-triangle-exclamation"></i> Alert</h6>
                            <p class="mb-0">You clean this vehicle</p>
                        </div>
                    `)
                }
            }
        });
    }

    const post_clean = () => {
        const vehicle_id = $('#vehicle_holder').val()

        if(vehicle_id !== "-" && $('#clean_by').val() !== "-"){
            const clean_end_time = $('#clean_end_time').val()

            Swal.showLoading();
            $.ajax({
                url: `/api/v1/clean`,
                type: 'POST',
                contentType: "application/json",
                data: JSON.stringify({
                    vehicle_id: vehicle_id,
                    clean_desc: $('#clean_desc').val(),  
                    clean_by: $('#clean_by_holder').val(), 
                    clean_tools: null,  
                    is_clean_body: $('#is_clean_body').is(':checked'), 
                    is_clean_window: $('#is_clean_window').is(':checked'), 
                    is_clean_dashboard: $('#is_clean_dashboard').is(':checked'), 
                    is_clean_tires: $('#is_clean_tires').is(':checked'), 
                    is_clean_trash: $('#is_clean_trash').is(':checked'), 
                    is_clean_engine: $('#is_clean_engine').is(':checked'), 
                    is_clean_seat: $('#is_clean_seat').is(':checked'),
                    is_clean_carpet: $('#is_clean_carpet').is(':checked'),
                    is_clean_pillows: $('#is_clean_pillows').is(':checked'),
                    clean_address: $('#clean_address').val(), 
                    clean_start_time: formatDateTimeAPI($('#clean_start_time').val()), 
                    clean_end_time: clean_end_time ? formatDateTimeAPI(clean_end_time) : null, 
                    is_fill_window_cleaning_water: $('#is_fill_window_cleaning_water').is(':checked'), 
                    is_clean_hollow: $('#is_clean_hollow').is(':checked')
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
                        window.location.href = '/clean'
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 400){
                        failedMsg('create clean')
                    } else {
                        // ....
                    }
                }
            });
        } else {
            failedMsg('create clean : you must select an item')
        }
    }
</script>