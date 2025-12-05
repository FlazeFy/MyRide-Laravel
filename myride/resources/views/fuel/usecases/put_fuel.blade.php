<div class="modal fade" id="update-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bold" id="exampleModalLabel">Edit Fuel</h4>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <input hidden id="fuel_id">
                <form id="form-update-fuel">
                    <label>Vehicle Name & Plate Number</label>
                    <select class="form-select" name="vehicle_holder" id="vehicle_holder" aria-label="Default select example"></select>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <label>Fuel Brand</label>
                            <select class="form-select" name="fuel_brand" id="fuel_brand_holder" aria-label="Default select example"></select>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <label>Fuel Type</label>
                            <select class="form-select" name="fuel_type" id="fuel_type_holder" aria-label="Default select example"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <label>Fuel RON</label>
                            <input class="form-control" name="fuel_ron" id="fuel_ron">
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <label>Volume (L)</label>
                            <input class="form-control" name="fuel_volume" id="fuel_volume" type="number" min="1" value="1">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <label>Total Price</label>
                            <input class="form-control" name="fuel_price_total" id="fuel_price_total">
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
    $(document).on('click','.btn-update',function(){
        callModal('update-modal')
        get_vehicle_name_opt(token)
        get_context_opt('fuel_type,fuel_brand',token)
        const vehicle_plate_number = $(this).data('vehicle-plate-number')

        $('#vehicle_holder option').each(function () {
            const optionText = $(this).text().trim()
            const optionPrefix = optionText.split(' - ')[0].trim()

            if (optionPrefix === vehicle_plate_number) {
                $('#vehicle_holder').val($(this).val())
                return false
            }
        })
        $('#fuel_id').val($(this).data('id'))
        $('#fuel_ron').val($(this).data('fuel-ron'))
        $('#fuel_brand_holder').val($(this).data('fuel-brand'))
        $('#fuel_type_holder').val($(this).data('fuel-type'))
        $('#fuel_price_total').val($(this).data('fuel-price-total'))
        $('#fuel_volume').val($(this).data('fuel-volume'))
        get_context_opt(`fuel_type_${$(this).data('fuel-brand')}`,token)
    })

    $(document).on('change','#fuel_brand_holder', function(){
        const val = $(this).val()
        get_context_opt(`fuel_type_${val}`,token)
    })

    $(document).on('click','#submit_update-btn', function(){
        const id = $('#fuel_id').val()
        put_fuel(id)
    })
    const put_fuel = (id) => {
        const vehicle_id = $('#vehicle_holder').val()
        const fuel_category = $('#fuel_category_holder').val()

        if(vehicle_id !== "-" && fuel_category !== "-"){
            Swal.showLoading();
            $.ajax({
                url: `/api/v1/fuel/${id}`,
                type: 'PUT',
                data: $('#form-update-fuel').serialize(),
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
                        window.location.href = '/fuel'
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status === 500){
                        generate_api_error(response, true)
                    } else {
                        failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                    }
                }
            });
        } else {
            failedMsg('update fuel : you must select an item')
        }
    }
</script>