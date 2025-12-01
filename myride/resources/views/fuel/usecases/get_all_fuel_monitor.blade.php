<h2>Fuel Monitor</h2><hr>
<div id="stats_fuel_status_holder" class="row"></div>

<script>
    const get_fuel_status = () => {
        const holder = 'stats_fuel_status_holder'

        Swal.showLoading();
        $.ajax({
            url: `/api/v1/vehicle/fuel`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
                $(`#${holder}`).empty()
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                
                data.forEach(dt => {
                    $(`#${holder}`).append(`
                        <div class="col-xl-6 col-lg-12 text-center d-block mx-auto">
                            <p class="text-secondary mb-0">Max Cap : <b>${dt.vehicle_fuel_capacity}L</b></p>
                            <div id="stats_${dt.id}" class="mb-3"></div>
                            <span class="plate-number">${dt.vehicle_plate_number}</span>
                        </div>
                    `)

                    if(dt.vehicle_fuel_status !== 'Not Monitored'){
                        let percentage

                        if(dt.vehicle_fuel_status === 'Fuel'){
                            percentage = 100
                        } else if(dt.vehicle_fuel_status === 'High'){
                            percentage = 75
                        } else if(dt.vehicle_fuel_status === 'Normal'){
                            percentage = 50
                        } else if(dt.vehicle_fuel_status === 'Low'){
                            percentage = 25
                        } else {
                            percentage = 0
                        }

                        generate_semi_gauge_chart(null, `stats_${dt.id}`, percentage)
                    } else {
                        $(`#stats_${dt.id}`).append(`
                            <div class="alert alert-danger">Not Monitored</div>
                        `)
                    }
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()

                if(response.status != 404){
                    generate_api_error(response, true)
                } else {
                    template_alert_container(holder, 'no-data', "No vehicle found", 'add a vehicle', '<i class="fa-solid fa-gas-pump"></i>','/vehicle/add')
                }
            }
        });
    };
    get_fuel_status()
</script>