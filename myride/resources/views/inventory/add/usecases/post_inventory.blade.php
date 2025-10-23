<h2>Add Inventory</h2>
<form id="form-add-inventory">
    <div class="row">
        <div class="col-xl-6 col-lg-12 pb-4">
            <div class="row">
                <div class="col-12">
                    <label>Vehicle Name & Plate Number</label>
                    <select class="form-select" name="vehicle_holder" id="vehicle_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Type</label>
                    <input class="form-control" name="vehicle_type" id="vehicle_type" readonly>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Category</label>
                    <input class="form-control" name="vehicle_category" id="vehicle_category" readonly>
                </div>
            </div>
            <hr>
            @include('inventory.add.usecases.get_attached_inventory')
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Inventory Category</label>
                    <select class="form-select" name="inventory_category" id="inventory_category_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Inventory Storage</label>
                    <select class="form-select" name="inventory_storage" id="inventory_storage_holder" aria-label="Default select example"></select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label>Qty</label>
                    <input class="form-control" name="inventory_qty" id="inventory_qty" type="number" min="1" value="1">
                </div>
                <div class="col-lg-9 col-md-9 col-sm-12">
                    <label>Inventory Name</label>
                    <input class="form-control" name="inventory_name" id="inventory_name">
                </div>
            </div>
            <hr>
            <label>Inventory Image</label>
            <input type="file">
            <a class="btn btn-success rounded-pill py-3 w-100 mt-3" id="submit-add-inventory-btn"><i class="fa-solid fa-floppy-disk"></i> Save Inventory</a>
        </div>
    </div>
</form>

<script type="text/javascript">    
    $(document).on('click','#submit-add-inventory-btn', function(){
        post_inventory()
    })
    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        get_vehicle_detail(id)
    })
    $(document).on('change','#inventory_category_holder', function(){
        const val = $(this).val()
    })

    get_vehicle_name_opt(token)
    get_context_opt('inventory_category,inventory_storage',token)

    const post_inventory = () => {
        const vehicle_id = $('#vehicle_holder').val()
        const inventory_category = $('#inventory_category_holder').val()

        if(vehicle_id !== "-" && inventory_category !== "-"){
            Swal.showLoading();
            $.ajax({
                url: `/api/v1/inventory`,
                type: 'POST',
                contentType: "application/json",
                data: JSON.stringify({
                    vehicle_id: vehicle_id,
                    gudangku_inventory_id: null,
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
                        failedMsg('create inventory')
                    } else {
                        failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                    }
                }
            });
        } else {
            failedMsg('create inventory : you must select an item')
        }
    }
</script>