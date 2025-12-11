<div class="modal fade" id="update-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bold" id="exampleModalLabel">Edit Wash</h4>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <input hidden id="wash_id">
                <label>Vehicle Name & Plate Number</label>
                <select class="form-select" name="vehicle_holder" id="vehicle_holder" aria-label="Default select example"></select>
                <div class="row">
                    <div class="col-6">
                        <label>Start At</label>
                        <input class="form-control" type="datetime-local" name="wash_start_time" id="wash_start_time">
                    </div>
                    <div class="col-6">
                        <label>End At</label>
                        <input class="form-control" type="datetime-local" name="wash_end_time" id="wash_end_time">
                    </div>
                    <div class="col-6">
                        <label>Wash By</label>
                        <select class="form-select" name="wash_by" id="wash_by_holder" aria-label="Default select example">
                            <option>-</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label>Wash Price</label>
                        <input class="form-control" type="number" min="1" id="wash_price" name="wash_price">
                    </div>
                </div>
                <label>Address</label>
                <textarea class="form-control" name="wash_address" id="wash_address" required></textarea>
                <label>Description</label>
                <textarea class="form-control" name="wash_desc" id="wash_desc" required></textarea>
                <label>Checklist</label>
                <div class="bordered rounded p-3">
                    <div class="row" id="checklist-holder"></div>
                </div>
                <hr>
                <div class="d-grid d-md-inline-block">
                    <button class="btn btn-success rounded-pill px-4 w-100 w-md-auto" id="submit_update-btn"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on('click','.btn-update',async function(){
            callModal('update-modal')
            await get_vehicle_name_opt(token)
            await get_context_opt('wash_by',token)
            set_checklist_holder()
            const vehicle_plate_number = $(this).data('vehicle-plate-number')

            $('#vehicle_holder option').each(function () {
                const optionText = $(this).text().trim()
                const optionPrefix = optionText.split(' - ')[0].trim()

                if (optionPrefix === vehicle_plate_number) {
                    $('#vehicle_holder').val($(this).val())
                    return false
                }
            })
            $('#wash_id').val($(this).data('id'))
            $('#wash_by').val($(this).data('wash-by'))
            $('#wash_address').val($(this).data('wash-address'))
            $('#wash_desc').val($(this).data('wash-desc'))
            $('#wash_tools').val($(this).data('wash-tools'))
            $('#wash_price').val($(this).data('wash-price'))
            $('#wash_start_time').val($(this).data('wash-start-time'))
            $('#wash_end_time').val($(this).data('wash-end-time'))
            $('#is_wash_body').prop('checked', $(this).data('is-wash-body'))
            $('#is_wash_window').prop('checked', $(this).data('is-wash-window'))
            $('#is_wash_dashboard').prop('checked', $(this).data('is-wash-dashboard'))
            $('#is_wash_tires').prop('checked', $(this).data('is-wash-tires'))
            $('#is_wash_trash').prop('checked', $(this).data('is-wash-trash'))
            $('#is_wash_engine').prop('checked', $(this).data('is-wash-engine'))
            $('#is_wash_seat').prop('checked', $(this).data('is-wash-seat'))
            $('#is_wash_carpet').prop('checked', $(this).data('is-wash-carpet'))
            $('#is_wash_pillows').prop('checked', $(this).data('is-wash-pillows'))
            $('#is_wash_hollow').prop('checked', $(this).data('is-wash-hollow'))
            $('#is_fill_window_washing_water').prop('checked', $(this).data('is-fill-window-washing-water'))
        })

        $(document).on('click','#submit_update-btn', function(){
            const id = $('#wash_id').val()
            put_wash(id)
        })
        const put_wash = (id) => {
            const vehicle_id = $('#vehicle_holder').val()

            if(vehicle_id !== "-" && $('#wash_by').val() !== "-"){
                const wash_end_time = $('#wash_end_time').val()

                Swal.showLoading()
                $.ajax({
                    url: `/api/v1/wash/${id}`,
                    type: 'PUT',
                    contentType: "application/json",
                    data: JSON.stringify({
                        vehicle_id: vehicle_id,
                        wash_desc: $('#wash_desc').val(),  
                        wash_by: $('#wash_by_holder').val(), 
                        wash_tools: null,  
                        is_wash_body: $('#is_wash_body').is(':checked'), 
                        is_wash_window: $('#is_wash_window').is(':checked'), 
                        is_wash_dashboard: $('#is_wash_dashboard').is(':checked'), 
                        is_wash_tires: $('#is_wash_tires').is(':checked'), 
                        is_wash_trash: $('#is_wash_trash').is(':checked'), 
                        is_wash_engine: $('#is_wash_engine').is(':checked'), 
                        is_wash_seat: $('#is_wash_seat').is(':checked'),
                        is_wash_carpet: $('#is_wash_carpet').is(':checked'),
                        is_wash_pillows: $('#is_wash_pillows').is(':checked'),
                        wash_address: $('#wash_address').val(), 
                        wash_price: $('#wash_price').val(), 
                        wash_start_time: formatDateTimeAPI($('#wash_start_time').val()), 
                        wash_end_time: wash_end_time ? formatDateTimeAPI(wash_end_time) : null, 
                        is_fill_window_washing_water: $('#is_fill_window_washing_water').is(':checked'), 
                        is_wash_hollow: $('#is_wash_hollow').is(':checked')
                    }),
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
                            window.location.href = '/wash'
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
                failedMsg('create wash : you must select an item')
            }
        }
    })
</script>