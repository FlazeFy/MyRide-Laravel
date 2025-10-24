<h2>Vehicle Readiness</h2>
<table class="table text-center table-bordered">
    <thead>
        <tr>
            <th scope="col" style="width: 140px;">Plate Number</th>
            <th scope="col">Vehicle Name & Type</th>
            <th scope="col">Status</th>
            <th scope="col">Fuel</th>
            <th scope="col">Capacity</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody id="vehicle_readiness-holder"></tbody>
</table>

<script>
    const get_all_trip = () => {
        const holder = 'vehicle_readiness-holder'
        Swal.showLoading()
        $.ajax({
            url: `/api/v1/vehicle/readiness`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
            },
            success: function(response) {
                Swal.close()
                const data = response.data.data
                $(`#${holder}`).empty()

                data.forEach(dt => {
                    $(`#${holder}`).append(`
                        <tr>
                            <td><span class="plate-number">${dt.vehicle_plate_number}</span></td>
                            <td class="text-start">
                                <p class="mb-0" style="font-weight:500;">
                                    ${dt.vehicle_name} ${dt.vehicle_transmission == 'Automatic' ? 'AT' : dt.vehicle_transmission == 'Manual' ? 'MT' : dt.vehicle_transmission}
                                    ${dt.deleted_at ? '<span class="chip-mini bg-danger">Deleted</span>' : ''}
                                </p>
                                <p class="mb-0 text-secondary">${dt.vehicle_type}</p>
                            </td>
                            <td><span class="chip bg-${dt.vehicle_status == 'Available' ? 'success': dt.vehicle_status == 'Reserved' ? 'warning':'danger'}">${dt.vehicle_status}</span></td>
                            <td><span class="chip bg-${dt.vehicle_fuel_status == 'Full' || dt.vehicle_fuel_status == 'High' ? 'success': dt.vehicle_fuel_status == 'Normal' ? 'warning':'danger'}">${dt.vehicle_fuel_status}</span></td>
                            <td><i class="fa-solid fa-users"></i> ${dt.vehicle_capacity}</td>
                            <td><a class="btn btn-success px-4"><i class="fa-solid fa-play"></i></a></td>
                        </tr>
                    `)
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status !== 404){
                    failedMsg('get the vehicle readiness')
                } else {
                    $(`#${holder}`).html(`<tr><td colspan="6" id="msg-${holder}"></td></tr>`)
                    template_alert_container(`msg-${holder}`, 'no-data', "No vehicle found", 'add a trip', '<i class="fa-solid fa-car"></i>','/vehicle/add')
                }
            }
        });
    }
    get_all_trip()
</script>