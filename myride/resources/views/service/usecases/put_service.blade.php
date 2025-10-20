<div class="modal fade" id="update-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bold" id="exampleModalLabel">Edit Service</h4>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <input hidden id="service_id">
                <label>Vehicle Name & Plate Number</label>
                <select class="form-select" name="vehicle_holder" id="vehicle_holder" aria-label="Default select example"></select>
                <label>Service Category</label>
                <select class="form-select" name="service_category" id="service_category_holder" aria-label="Default select example"></select>
                <label>Service Location</label>
                <input class="form-control" name="service_location" id="service_location">
                <label>Price Total</label>
                <input class="form-control" name="service_price_total" id="service_price_total" type="number" min="1" value="1">
                <label>Service Note</label>
                <textarea class="form-control" name="service_note" id="service_note"></textarea>
                <label>Remind At</label>
                <input class="form-control" type="datetime-local" name="remind_at" id="remind_at">
                <hr>
                <button class="btn btn-success rounded-pill px-4" id="submit_update-btn"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click','.btn-update',function(){
        const token = `<?= session()->get("token_key"); ?>`
        callModal('update-modal')
        get_vehicle_name_opt(token)
        get_context_opt('service_category,service_storage',token)

        $('#vehicle_holder option').each(function () {
            const optionText = $(this).text().trim()
            const optionPrefix = optionText.split(' - ')[0].trim()

            if (optionPrefix === $(this).data('vehicle-plate-number')) {
                $('#vehicle_holder').val($(this).val())
                return false
            }
        })
        $('#service_id').val($(this).data('id'))
        $('#service_name').val($(this).data('service-name'))
        $('#service_price_total').val($(this).data('service-price-total'))
        $('#service_category_holder').val($(this).data('service-category'))
        $('#service_note').val($(this).data('service-note'))
        $('#service_location').val($(this).data('service-location'))
        $('#remind_at').val($(this).data('remind-at'))
    })

    $(document).on('click','#submit_update-btn', function(){
        const id = $('#service_id').val()
        put_service(id)
    })
    const put_service = (id) => {
        const vehicle_id = $('#vehicle_holder').val()
        const service_category = $('#service_category_holder').val()

        if(vehicle_id !== "-" && service_category !== "-"){
            Swal.showLoading();
            $.ajax({
                url: `/api/v1/service/${id}`,
                type: 'PUT',
                contentType: "application/json",
                data: JSON.stringify({
                    vehicle_id: vehicle_id,
                    service_note: $("#service_note").val(),
                    service_category: $("#service_category_holder").val(),
                    service_price_total: $("#service_price_total").val(),
                    service_location: $("#service_location").val()
                }),
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
                },
                success: function(response) {
                    Swal.close()
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success"
                    }).then(() => {
                        window.location.href = '/service'
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status === 500){
                        failedMsg('update service')
                    } else {
                        failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                    }
                }
            });
        } else {
            failedMsg('update service : you must select an item')
        }
    }
</script>