<div class="modal fade" id="update-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bold" id="exampleModalLabel">Edit Inventory</h4>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <input hidden id="inventory_id">
                <label>Vehicle Name & Plate Number</label>
                <select class="form-select" name="vehicle_holder" id="vehicle_holder" aria-label="Default select example"></select>
                <label>Inventory Category</label>
                <select class="form-select" name="inventory_category" id="inventory_category_holder" aria-label="Default select example">
                    <option>-</option>
                </select>
                <label>Inventory Storage</label>
                <select class="form-select" name="inventory_storage" id="inventory_storage_holder" aria-label="Default select example"></select>
                <label>Qty</label>
                <input class="form-control" name="inventory_qty" id="inventory_qty" type="number" min="1" value="1">
                <label>Inventory Name</label>
                <input class="form-control" name="inventory_name" id="inventory_name">
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
        get_context_opt('inventory_category,inventory_storage',token)

        $('#vehicle_holder option').each(function () {
            const optionText = $(this).text().trim()
            const optionPrefix = optionText.split(' - ')[0].trim()

            if (optionPrefix === $(this).data('vehicle-plate-number')) {
                $('#vehicle_holder').val($(this).val())
                return false
            }
        })
        $('#inventory_id').val($(this).data('id'))
        $('#inventory_name').val($(this).data('inventory-name'))
        $('#inventory_qty').val($(this).data('inventory-qty'))
        $('#inventory_category_holder').val($(this).data('inventory-category'))
        $('#inventory_storage_holder').val($(this).data('inventory-storage'))
    })

    $(document).on('click','#submit_update-btn', function(){
        const id = $('#inventory_id').val()
        put_inventory(id)
    })
    const put_inventory = (id) => {
        const vehicle_id = $('#vehicle_holder').val()
        const inventory_category = $('#inventory_category_holder').val()

        if(vehicle_id !== "-" && inventory_category !== "-"){
            Swal.showLoading();
            $.ajax({
                url: `/api/v1/inventory/${id}`,
                type: 'PUT',
                contentType: "application/json",
                data: JSON.stringify({
                    vehicle_id: vehicle_id,
                    inventory_name: $("#inventory_name").val(),
                    inventory_category: $("#inventory_category_holder").val(),
                    inventory_qty: $("#inventory_qty").val(),
                    inventory_storage: $("#inventory_storage_holder").val()
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
                        window.location.href = '/inventory'
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status === 500){
                        failedMsg('update inventory')
                    } else {
                        failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                    }
                }
            });
        } else {
            failedMsg('update inventory : you must select an item')
        }
    }
</script>