<a class="btn btn-success" data-bs-target="#assign_driver-modal" data-bs-toggle="modal"><i class="fa-solid fa-plus"></i> Assign Driver</a>
<div class="modal fade" id="assign_driver-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bold" id="exampleModalLabel">Assigned Driver</h4>
                <button class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="min-width: 180px">Vehicle</th>
                                <th style="min-width: 400px">Driver</th>
                                <th style="min-width: 120px">Action</th>
                            </tr>
                        </thead>
                        <tbody id="assigned_driver-holder"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const get_all_assigned_driver = () => {
        const holder = 'assigned_driver-holder'

        Swal.showLoading()
        $.ajax({
            url: `/api/v1/driver/vehicle/list`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                $(`#${holder}`).empty()
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                
                data.vehicle.forEach(dt => {
                    const listDriver = data.assigned ? data.assigned.filter(asg => asg.vehicle_id === dt.id).map(asg => `
                        <div class="d-inline-flex align-items-center pt-2">
                            <div class="text-start">
                                <div>
                                    <span class="plate-number me-0"><i class="fa-solid fa-user-tie"></i> ${asg.username}</span>
                                    <a class="btn btn-danger p-0 pt-1 btn-remove-assigned-driver" data-id="${asg.id}" data-vehicle="<b>(${dt.vehicle_plate_number})</b> ${dt.vehicle_name}" data-driver="<b>${asg.username}</b>" style="height: var(--spaceJumbo); width: var(--spaceJumbo);"><i class="fa-solid fa-circle-xmark fa-sm"></i></a>
                                </div>
                                <p class="text-secondary mt-1 mb-0 ms-2 fw-bold">${asg.fullname}</p>
                            </div>
                        </div>
                    `).join("") : null

                    $(`#${holder}`).append(`
                        <tr>
                            <td class="pt-2">
                                <span class="plate-number">${dt.vehicle_plate_number}</span>
                                <p class="text-secondary mt-2 mb-0 fw-bold">${dt.vehicle_name}</p>
                            </td>
                            <td class="text-start">${listDriver === "" ? `<span class="no-msg-text mx-2">- No driver has assigned -</span>` : listDriver}</td>
                            <td>
                                ${
                                    listDriver === "" && data.driver && data.driver.length > 0 ?
                                    `<a class="btn btn-success pt-2 pb-1 px-3 ms-2 btn-assigned-driver-vehicle" data-vehicle_id="${dt.id}" data-vehicle="<b>(${dt.vehicle_plate_number})</b> ${dt.vehicle_name}" style="font-size: var(--textMD)"
                                        data-driver='${JSON.stringify(data.driver)}' data-vehicle_plate_number="${dt.vehicle_plate_number}"
                                        ><i class="fa-solid fa-plus"></i> Driver</a>`: "<p class='no-msg-text mx-2'>- Assigned -</p>"
                                }
                            </td>
                        </tr>
                    `)
                })
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()

                if(response.status !== 404){
                    generateApiError(response, true)
                } else {
                    $(`#${holder}`).html(`<tr><td colspan="5" id="msg-${holder}"></td></tr>`)
                    templateAlertContainer(`msg-${holder}`, 'no-data', "No driver found", 'add a driver', '<i class="fa-solid fa-user"></i>','/driver/add')
                }
            }
        })
    }
    get_all_assigned_driver()
</script>