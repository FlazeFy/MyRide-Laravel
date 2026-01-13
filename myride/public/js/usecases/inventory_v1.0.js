const getVehicleNameOption = (token) => {
    return new Promise((resolve, reject) => {
        Swal.showLoading()
        const ctx = 'vehicle_name_temp'
        const ctx_holder = 'vehicle_holder'

        const generateVehicleList = (holder, data) => {
            $(`#${holder}`).empty().append(`<option selected>-</option>`)
            data.forEach(el => {
                $(`#${holder}`).append(`<option value="${el.id}">${el.vehicle_plate_number} - ${el.vehicle_name}</option>`)
            })

            const params = new URLSearchParams(window.location.search)
            const vehicle_id = params.get('vehicle_id')
            vehicle_id && $(`#${holder}`).val(vehicle_id)
            resolve()
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/vehicle/name`,
                type: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${token}`
                },
                success: function (response) {
                    Swal.close()
                    const data = response.data
                    localStorage.setItem(ctx, JSON.stringify(data))
                    localStorage.setItem(`last-hit-${ctx}`, Date.now())

                    generateVehicleList(ctx_holder, data)
                },
                error: function (response) {
                    Swal.close()
                    generateApiError(response, true)
                    reject(response)
                }
            })
        }

        if (ctx in localStorage) {
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if (((now - lastHit) / 1000) < statsFetchRestTime) {
                const data = JSON.parse(localStorage.getItem(ctx))

                if (data) {
                    generateVehicleList(ctx_holder, data)
                } else {
                    failedMsg(`get the vehicle list`)
                    reject("No cached data")
                }
                Swal.close()
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    })
}

const getDriverNameOption = (token) => {
    Swal.showLoading()
    const ctx = 'driver_name_temp'
    const ctx_holder = 'driver_holder'

    const generateDriverList = (holder,data) => {
        $(`#${holder}`).html(`<option value="-">-</option>`)
        data.forEach(el => {
            $(`#${holder}`).append(`<option value="${el.id}">${el.username} - ${el.fullname}</option>`)
        })
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

                generateDriverList(ctx_holder,data)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                generateApiError(response, true)
            }
        })
    }

    if (ctx in localStorage){
        const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
        const now = Date.now()

        if (((now - lastHit) / 1000) < statsFetchRestTime){
            Swal.close()
            const data = JSON.parse(localStorage.getItem(ctx))

            if(data){
                generateDriverList(ctx_holder,data)
            } else {
                failedMsg(`get the driver list`)
            }
        } else {
            fetchData()
        }
    } else {
        fetchData()
    }
}

const getVehicleDetail = (id) => {
    const resetSelectedOption = target => target.forEach(dt => $(`#${dt}`).val(''))

    if (id !== "-"){
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
                generateApiError(response, true)
            }
        });
    } else {
        resetSelectedOption(['vehicle_type','vehicle_category'])
    }
}

const getDictionaryByContextOption = (context, token) => {
    return new Promise((resolve, reject) => {
        Swal.showLoading()
        let ctx_holder

        if (context.includes(',')) {
            ctx_holder = []
            context = context.split(',')
            ctx_holder.push(...context.map(el => `${el}_holder`))
        } else {
            ctx_holder = context.includes('fuel_type_') ? 'fuel_type_holder' : `${context}_holder`
        }

        const generateDictionaryContextList = (holder, data) => {
            if (Array.isArray(holder)) {
                holder.forEach(dt => {
                    $(`#${dt}`).empty().append(`<option>-</option>`)
                    data.forEach(el => {
                        if (el.dictionary_type === dt.replace('_holder','')) {
                            $(`#${dt}`).append(`<option value="${el.dictionary_name}">${el.dictionary_name}</option>`)
                        }
                    })
                })
            } else {
                $(`#${holder}`).empty().append(`<option>-</option>`)
                data.forEach(el => {
                    $(`#${holder}`).append(`<option value="${el.dictionary_name}">${el.dictionary_name}</option>`)
                })
            }
            resolve()
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/dictionary/type/${context}`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                },
                success: function (response) {
                    Swal.close()
                    const data = response.data
                    localStorage.setItem(ctx_holder, JSON.stringify(data))
                    localStorage.setItem(`last-hit-${ctx_holder}`, Date.now())

                    generateDictionaryContextList(ctx_holder, data)
                },
                error: function (response) {
                    Swal.close()
                    generateApiError(response, true)
                    reject(response)
                }
            })
        }

        if (ctx_holder in localStorage) {
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx_holder}`))
            const now = Date.now()

            if (((now - lastHit) / 1000) < statsFetchRestTime) {
                const data = JSON.parse(localStorage.getItem(ctx_holder))

                if (data) {
                    generateDictionaryContextList(ctx_holder, data)
                } else {
                    failedMsg(`get the ${context} list`)
                    reject("No cached data")
                }
                Swal.close()
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    })
}