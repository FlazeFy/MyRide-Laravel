<div class="modal fade" id="update-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bold" id="exampleModalLabel">Edit Trip</h4>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <input hidden id="trip_id">
                <form id="form-update-trip">
                    <div class="row">
                        <div class="col-12">
                            <label>Vehicle Name</label>
                            <select class="form-select" name="vehicle_id" id="vehicle_holder" aria-label="Default select example"></select>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <label>Trip Category</label>
                            <select class="form-select" name="trip_category" id="trip_category_holder" aria-label="Default select example"></select>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <label>Driver</label>
                            <select class="form-select" name="driver_id" id="driver_holder" aria-label="Default select example"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <label>Description</label>
                            <textarea class="form-control" name="trip_desc" id="trip_desc" required></textarea>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <label>Person</label>
                            <textarea class="form-control" name="trip_person" id="trip_person" required></textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <label>Trip Origin Name</label>
                            <input class="form-control" name="trip_origin_name" id="trip_origin_name" requried>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <label>Trip Destination Name</label>
                            <input class="form-control" name="trip_destination_name" id="trip_destination_name" requried>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <label>Trip Origin Coordinate</label>
                            <input class="form-control" name="trip_origin_coordinate" id="trip_origin_coordinate" requried>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <label>Trip Destination Coordinate</label>
                            <input class="form-control" name="trip_destination_coordinate" id="trip_destination_coordinate" requried>
                        </div>
                    </div>
                </form>
                <hr>
                <button class="btn btn-success rounded-pill px-4" id="submit_update-btn"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click','.btn-update',async function(){
        callModal('update-modal')

        await get_vehicle_name_opt(token)
        await get_context_opt('trip_category',token)
        get_driver_name_opt(token)

        const vehicle_plate_number = $(this).data('vehicle-plate-number')

        $('#vehicle_holder option').each(function () {
            const optionText = $(this).text().trim()
            const optionPrefix = optionText.split(' - ')[0].trim()

            if (optionPrefix === vehicle_plate_number) {
                $('#vehicle_holder').val($(this).val())
                return false
            }
        })
        $('#trip_id').val($(this).data('id'))
        $('#trip_desc').val($(this).data('trip-desc'))
        $('#trip_category_holder').val($(this).data('trip-category'))
        $('#trip_person').val($(this).data('trip-person'))
        $('#trip_origin_name').val($(this).data('trip-origin-name'))
        $('#trip_origin_coordinate').val($(this).data('trip-origin-coordinate'))
        $('#trip_destination_name').val($(this).data('trip-destination-name'))
        $('#trip_destination_coordinate').val($(this).data('trip-destination-coordinate'))
    })

    $(document).on('click','#submit_update-btn', function(){
        const id = $('#trip_id').val()
        put_trip(id)
    })
    const put_trip = (id) => {
        const vehicle_id = $('#vehicle_holder').val()
        const trip_category = $('#trip_category_holder').val()

        if(vehicle_id !== "-" && trip_category !== "-"){
            Swal.showLoading();
            $.ajax({
                url: `/api/v1/trip/${id}`,
                type: 'PUT',
                data: $('#form-update-trip').serialize(),
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                },
                success: function(response) {
                    Swal.close()
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success"
                    }).then(() => {
                        window.location.href = '/trip'
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    generate_api_error(response, true)
                }
            });
        } else {
            failedMsg('update trip : you must select an item')
        }
    }
</script>