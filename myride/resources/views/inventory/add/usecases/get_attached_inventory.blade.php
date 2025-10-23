<label>Attached Inventory</label>
<table class="table">
    <thead>
        <tr>
            <th scope="col">Inventory Name</th>
            <th scope="col">Category</th>
            <th scope="col">Storage</th>
            <th scope="col">Qty</th>
        </tr>
    </thead>
    <tbody id="list_attached_inventory-holder">
        <tr><th scope="row" colspan="4" class="fst-italic fw-normal">- No Inventory Found -</th></tr>
    </tbody>
</table>

<script>
    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        if(id !== "-"){
            get_vehicle_attached_inventory(id)
        } else {
            $(`#${holder}`).html(`<th scope="row" colspan="4" class="no-msg-text">- No Inventory Found -</th>`)
        }
    })

    const get_vehicle_attached_inventory = (id) => {
        $(`#${holder}`).empty()
        Swal.showLoading()

        $.ajax({
            url: `/api/v1/inventory/vehicle/${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                
                data.forEach(dt => {
                    $(`#${holder}`).append(`
                        <tr>
                            <td scope="col">${dt.inventory_name}</td>
                            <td scope="col">${dt.inventory_category}</td>
                            <td scope="col">${dt.inventory_storage}</td>
                            <td scope="col">${dt.inventory_qty}</td>
                        </tr>
                    `)
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status !== 404){
                    failedMsg('get the inventory')
                } else {
                    $(`#${holder}`).html(`<th scope="row" colspan="4" class="no-msg-text">- No Inventory Found -</th>`)
                }
            }
        });
    }
</script>