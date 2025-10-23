<label>Last Clean</label>
<div id="last_clean-holder">
    <div class="no-msg-text">- No Clean Found -</div>
</div>

<script>
    const clean_holder = 'last_clean-holder'

    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        if(id !== "-"){
            get_vehicle_last_clean(id)
        } else {
            $(`#${clean_holder}`).html('<div class="no-msg-text">- No Clean Found -</div>')
        }
    })
    const get_vehicle_last_clean = (id) => {
        Swal.showLoading();
        $.ajax({
            url: `/api/v1/clean/last?vehicle_id=${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
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
                
                $(`#${clean_holder}`).html(`
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
                    $(`#${clean_holder}`).html(`
                        <div class="container bg-danger">
                            <h6><i class="fa-solid fa-triangle-exclamation"></i> Alert</h6>
                            <p class="mb-0">You clean this vehicle</p>
                        </div>
                    `)
                }
            }
        });
    }
</script>