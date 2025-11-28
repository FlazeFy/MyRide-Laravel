<div class="modal fade" id="assign_driver_vehicle-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bold" id="exampleModalLabel">Choose Driver</h4>
                <button class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Driver</th>
                                <th style="width: 120px;">Assign</th>
                            </tr>
                        </thead>
                        <tbody id="list_driver-holder"></tbody>
                    </table>
                </div>
                <hr>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click','.btn-assigned-driver-vehicle',function(){
        const vehicle_id = $(this).data('vehicle_id')
        const vehicle_data = $(this).data('vehicle_data')
        const driver = $(this).data('driver')
        const vehicle_plate_number = $(this).data('vehicle_plate_number')
        const holder = 'list_driver-holder'

        $('#assign_driver_vehicle-modal').modal('show')
        $(`#${holder}`).empty()
        driver.forEach(dt => {
            $(`#${holder}`).append(`
                <tr>
                    <th class="pt-2">
                        <span class="plate-number"><i class="fa-solid fa-user-tie"></i> ${dt.username}</span>
                        <p class="text-secondary mt-2 mb-0 fw-bold">${dt.fullname}</p>
                    </th>
                    <th>
                        <a class="btn btn-success pt-2 pb-1 px-3 ms-2" id="submit_assigned_driver-modal" data-vehicle_id="${vehicle_id}" data-driver_id="${dt.id}" data-driver_fullname="${dt.fullname}" data-vehicle_plate_number="${vehicle_plate_number}" style="font-size:var(--textMD);">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </th>
                </tr>
            `)
        });
    })

    $(document).on('click','#submit_assigned_driver-modal',function(){
        const vehicle_id = $(this).data('vehicle_id')
        const driver_id = $(this).data('driver_id')
        const driver_fullname = $(this).data('driver_fullname')
        const vehicle_plate_number = $(this).data('vehicle_plate_number')

        Swal.fire({
            title: "Are you sure?",
            html: `Do you want to assign <b>"${driver_fullname}"</b> as the driver of <b>"${vehicle_plate_number}"</b>?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, assign him/her",
            cancelButtonText: "No, cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/v1/driver/vehicle',
                    type: 'POST',
                    data: {
                        vehicle_id: vehicle_id,
                        driver_id: driver_id,
                        relation_note: null
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader("Accept", "application/json")
                        xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                    },
                    success: function(response) {
                        Swal.fire("Success!", "Driver has successfully assigned", "success").then(() => window.location.reload())
                    },
                    error: function(response, jqXHR, textStatus, errorThrown) {
                        generate_api_error(response, true)
                    }
                });
            }
        });
    })
</script>