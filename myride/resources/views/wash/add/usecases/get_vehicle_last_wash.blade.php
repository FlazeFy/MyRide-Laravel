<label>Last wash</label>
<div id="last_wash-holder">
    <div class="no-msg-text">- No wash Found -</div>
</div>

<script>
    const wash_holder = 'last_wash-holder'
    messageAlertBox(wash_holder, "danger", "You must select a vehicle first")

    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        get_vehicle_last_wash(id)
    })

    const get_vehicle_last_wash = (id) => {
        if(id !== "-"){
            Swal.showLoading()
            $.ajax({
                url: `/api/v1/wash/last/${id}`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    let wash_checklist = ''

                    if (data.is_wash_body) {
                        wash_checklist += '<h6 class="chip bg-warning d-inline m-0">Body</h6> '
                    }
                    if (data.is_wash_window) {
                        wash_checklist += '<h6 class="chip bg-warning d-inline m-0">Window</h6> '
                    }
                    if (data.is_wash_dashboard) {
                        wash_checklist += '<h6 class="chip bg-warning d-inline m-0">Dashboard</h6> '
                    }
                    if (data.is_wash_tires) {
                        wash_checklist += '<h6 class="chip bg-warning d-inline m-0">Tires</h6> '
                    }
                    if (data.is_wash_trash) {
                        wash_checklist += '<h6 class="chip bg-warning d-inline m-0">Trash</h6> '
                    }
                    if (data.is_wash_engine) {
                        wash_checklist += '<h6 class="chip bg-warning d-inline m-0">Engine</h6> '
                    }
                    if (data.is_wash_seat) {
                        wash_checklist += '<h6 class="chip bg-warning d-inline m-0">Seat</h6> '
                    }
                    if (data.is_wash_carpet) {
                        wash_checklist += '<h6 class="chip bg-warning d-inline m-0">Carpet</h6> '
                    }
                    if (data.is_wash_pillows) {
                        wash_checklist += '<h6 class="chip bg-warning d-inline m-0">Pillows</h6> '
                    }
                    if (data.is_fill_window_washing_water) {
                        wash_checklist += '<h6 class="chip bg-warning d-inline m-0">Window washing Water</h6> '
                    }
                    if (data.is_wash_hollow) {
                        wash_checklist += '<h6 class="chip bg-warning d-inline m-0">Hollow</h6> '
                    }

                    wash_checklist = wash_checklist.replace(/, $/, '')
                    
                    $(`#${wash_holder}`).html(`
                        <div class="container-fluid bg-info">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="chip bg-success m-0">${data.wash_by}</div>
                                <p class="text-secondary text-dark mb-0">wash at ${getDateToContext(data.created_at,'calendar')}</p>
                            </div>
                            <p>${data.wash_desc}</p>
                            <div class="d-flex flex-wrap gap-2">${wash_checklist}</div>
                        </div>
                    `)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status !== 404){
                        generateApiError(response, true)
                    } else {
                        messageAlertBox(wash_holder, "danger", "You never wash this vehicle")
                    }
                }
            });
        } else {
            messageAlertBox(wash_holder, "danger", "You must select a vehicle first")
        }
    }
</script>