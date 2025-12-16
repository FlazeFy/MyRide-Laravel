<label>Last wash</label>
<div id="last_wash-holder">
    <div class="no-msg-text">- No wash Found -</div>
</div>

<script>
    const wash_holder = 'last_wash-holder'

    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        if(id !== "-"){
            get_vehicle_last_wash(id)
        } else {
            $(`#${wash_holder}`).html('<div class="no-msg-text">- No wash Found -</div>')
        }
    })
    const get_vehicle_last_wash = (id) => {
        Swal.showLoading();
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
                    <div class="container-fluid bg-success">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h5 class="mb-0">${data.wash_desc}</h5>
                                <p class="text-secondary text-dark mb-0">wash at ${getDateToContext(data.created_at,'calendar')}</p>
                            </div>
                            <h5 class="chip bg-info m-0">${data.wash_by}</h5>
                        </div>
                        <div class="d-flex flex-wrap gap-2">${wash_checklist}</div>
                    </div>
                `)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status != 404){
                    generateApiError(response, true)
                } else {
                    $(`#${wash_holder}`).html(`
                        <div class="container-fluid bg-danger">
                            <h6><i class="fa-solid fa-triangle-exclamation"></i> Alert</h6>
                            <p class="mb-0">You never wash this vehicle</p>
                        </div>
                    `)
                }
            }
        });
    }
</script>