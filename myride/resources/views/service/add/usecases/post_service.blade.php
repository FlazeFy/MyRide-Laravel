<h2>Add Service</h2>
<form id="form-add-service">
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
            <label>Service History</label>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Notes</th>
                        <th scope="col">Category</th>
                        <th scope="col">Info</th>
                    </tr>
                </thead>
                <tbody id="list_service_history">
                    <tr><th scope="row" colspan="4" class="fst-italic fw-normal">- No Service Found -</th></tr>
                </tbody>
            </table>
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Service Category</label>
                    <select class="form-select" name="service_category" id="service_category_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Total Price</label>
                    <input class="form-control" name="service_price_total" id="service_price_total">
                </div>
            </div>
            <label>Service Location</label>
            <input class="form-control" name="service_location" id="service_location">
            <label>Remind At</label>
            <input class="form-control" type="datetime-local" name="remind_at" id="remind_at">
            <label>Service Notes</label>
            <textarea class="form-control" name="service_note" id="service_note" style="min-height:120px;"></textarea>
            <hr>
            <a class="btn btn-success rounded-pill py-3 w-100 mt-3" id="submit-add-service-btn"><i class="fa-solid fa-floppy-disk"></i> Save Service</a>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).on('click','#submit-add-service-btn', function(){
        post_service()
    })
    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        get_vehicle_detail(id)
        get_vehicle_service_history(id)
    })
    $(document).on('change','#service_category_holder', function(){
        const val = $(this).val()
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
        const ctx = `service_temp`
        let ctx_holder

        if(context.includes(',')){
            ctx_holder = []
            context = context.split(',')
            context.forEach(el => {
                ctx_holder.push(`${el}_holder`)
            })
        } else {
            ctx_holder = `${context}_holder`
        }

        const generate_context_list = (holder,data) => {
            if(Array.isArray(holder)){
                holder.forEach(dt => {
                    $(`#${dt}`).empty().append(`<option>-</option>`)
                    data.forEach(el => {
                        el.dictionary_type === dt.replace('_holder','') && $(`#${dt}`).append(`<option value="${el.dictionary_name}">${el.dictionary_name}</option>`)
                    });
                });
            } else {
                $(`#${holder}`).empty().append(`<option>-</option>`)
                data.forEach(el => {
                    $(`#${holder}`).append(`<option value="${el.dictionary_name}">${el.dictionary_name}</option>`)
                });
            }
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
                    generate_context_list(ctx_holder,data)
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
                    generate_context_list(ctx_holder,data)
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
    get_context_opt('service_category')

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

    const get_vehicle_service_history = (id) => {
        const holder = 'list_service_history'
        $(`#${holder}`).empty()
        Swal.showLoading()

        $.ajax({
            url: `/api/v1/service/vehicle/${id}`,
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
                        <tr>
                            <td scope="col">${dt.service_note}</td>
                            <td scope="col" style="width:120px;">${dt.service_category}</td>
                            <td scope="col" class="text-start" style="width:150px;">
                                <h6>Price</h6>
                                <p>Rp. ${number_format(dt.service_price_total, 0, ',', '.')},00</p>
                                <h6>Location</h6>
                                <p>${dt.service_location}</p>
                            </td>
                        </tr>
                    `)
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status != 404){
                    $(`#${holder}`).html(`<th scope="row" colspan="4" class="fst-italic fw-normal">- No Service Found -</th>`)
                } else {
                    failedMsg('get the vehicle service history')
                }
            }
        });
    }

    const post_service = () => {
        const vehicle_id = $('#vehicle_holder').val()
        const service_category = $('#service_category_holder').val()

        if(vehicle_id !== "-" && service_category !== "-"){
            Swal.showLoading();
            $.ajax({
                url: `/api/v1/service`,
                type: 'POST',
                contentType: "application/json",
                data: JSON.stringify({
                    vehicle_id: vehicle_id,
                    service_note: $("#service_note").val(),
                    service_category: $("#service_category_holder").val(),
                    service_location: $("#service_location").val(),
                    service_price_total: $("#service_price_total").val(),
                    remind_at: formatDateTimeAPI($("#remind_at").val())
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
                        window.location.href = '/service'
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status === 500){
                        failedMsg('create service')
                    } else {
                        failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                    }
                }
            });
        } else {
            failedMsg('create service : you must select an item')
        }
    }
</script>