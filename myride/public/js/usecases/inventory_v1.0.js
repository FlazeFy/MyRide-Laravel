const get_vehicle_name_opt = (token) => {
    Swal.showLoading()
    const ctx = 'vehicle_name_temp'
    const ctx_holder = 'vehicle_holder'

    const generate_vehicle_list = (holder,data) => {
        $(`#${holder}`).empty().append(`<option selected>-</option>`)
        data.forEach(el => {
            $(`#${holder}`).append(`<option value="${el.id}">${el.vehicle_plate_number} - ${el.vehicle_name}</option>`)
        });

        let params = new URLSearchParams(window.location.search)
        let vehicle_id = params.get('vehicle_id')
        if(vehicle_id){
            $(`#${holder}`).val(vehicle_id)
        }
    }

    const fetchData = () => {
        $.ajax({
            url: `/api/v1/vehicle/name`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
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

const get_driver_name_opt = (token) => {
    Swal.showLoading()
    const ctx = 'driver_name_temp'
    const ctx_holder = 'driver_holder'

    const generate_driver_list = (holder,data) => {
        $(`#${holder}`).html(`<option value="-">-</option>`)
        data.forEach(el => {
            $(`#${holder}`).append(`<option value="${el.id}">${el.username} - ${el.fullname}</option>`)
        });
    }

    const fetchData = () => {
        $.ajax({
            url: `/api/v1/driver/name`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                localStorage.setItem(ctx,JSON.stringify(data))
                localStorage.setItem(`last-hit-${ctx}`,Date.now())
                generate_driver_list(ctx_holder,data)
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
                generate_driver_list(ctx_holder,data)
                Swal.close()
            } else {
                Swal.close()
                failedMsg(`get the driver list`)
            }
        } else {
            fetchData()
        }
    } else {
        fetchData()
    }
}

const clean_select_opt = (target) => {
    target.forEach(dt => {
        $(`#${dt}`).val('')
    });
}

const get_vehicle_detail = (id) => {
    if(id !== "-"){
        Swal.showLoading()
        $.ajax({
            url: `/api/v1/vehicle/detail/${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
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
    } else {
        clean_select_opt(['vehicle_type','vehicle_category'])
    }
}

const get_context_opt = (context,token) => {
    Swal.showLoading()
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
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                localStorage.setItem(ctx_holder,JSON.stringify(data))
                localStorage.setItem(`last-hit-${ctx_holder}`,Date.now())
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

    if(ctx_holder in localStorage){
        const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx_holder}`))
        const now = Date.now()

        if(((now - lastHit) / 1000) < statsFetchRestTime){
            const data = JSON.parse(localStorage.getItem(ctx_holder))
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