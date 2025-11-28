<h2>All Inventory</h2>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col" style="width: 140px;">Vehicle</th>
                <th scope="col" style="width: 240px;">Inventory Name</th>
                <th scope="col">Qty</th>
                <th scope="col">Info</th>
                <th scope="col" style="width: 160px;">Properties</th>
                <th scope="col" style="width: 130px;">Action</th>
            </tr>
        </thead>
        <tbody id="inventory-holder"></tbody>
    </table>
</div>

<script>
    let page = 1

    const get_all_inventory = (page) => {
        const holder = 'inventory-holder'

        Swal.showLoading();
        $.ajax({
            url: `/api/v1/inventory`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                $(`#${holder}`).empty()
            },
            success: function(response) {
                Swal.close()
                const data = response.data.data
                
                data.forEach(dt => {
                    $(`#${holder}`).append(`
                        <tr>
                            <td>
                                <span class="plate-number">${dt.vehicle_plate_number}</span>
                                <p class="text-secondary mt-2 mb-0 fw-bold">${dt.vehicle_type}</p>
                            </td>
                            <td class="text-center">${dt.inventory_name}</td>
                            <td class="text-center">${dt.inventory_qty}</td>
                            <td class="text-start">
                                <h6 class="mb-0">Category</h6>
                                <p class="mb-0">${dt.inventory_category}</p>
                                <h6 class="mb-0">Storage</h6>
                                <p class="mb-0">${dt.inventory_storage}</p>
                            </td>
                            <td class="text-start">
                                <h6 class="mb-0">Created At</h6>
                                <p class="mb-0">${getDateToContext(dt.created_at,'calendar')}</p>
                                ${
                                    dt.updated_at ? `
                                        <h6 class="mb-0">Updated At</h6>
                                        <p class="mb-0">${getDateToContext(dt.updated_at,'calendar')}</p>
                                    ` : ''
                                }
                            </td>
                            <td>
                                ${dt.inventory_image_url != null ? `<a class="btn btn-primary" style="width:50px;"><i class="fa-solid fa-image"></i></a>` : ""}
                                <a class="btn btn-danger btn-delete" style="width:50px;" data-url="/api/v1/inventory/destroy/${dt.id}" data-context="Inventory"><i class="fa-solid fa-trash"></i></a>
                                <a class="btn btn-warning btn-update" style="width:50px;" 
                                    data-vehicle-plate-number="${dt.vehicle_plate_number}" data-id="${dt.id}" 
                                    data-inventory-name="${dt.inventory_name}" data-inventory-qty="${dt.inventory_qty}" 
                                    data-inventory-category="${dt.inventory_category}" data-inventory-storage="${dt.inventory_storage}"><i class="fa-solid fa-pen-to-square"></i></a>
                            </td>
                        </tr>
                    `)
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status != 404){
                    generate_api_error(response, true)
                } else {
                    $(`#${holder}`).html(`<tr><td colspan="6" id="msg-${holder}"></td></tr>`)
                    template_alert_container(`msg-${holder}`, 'no-data', "No inventory found", 'add a inventory', '<i class="fa-solid fa-boxes-stacked"></i>','/inventory/add')
                }
            }
        });
    };
    get_all_inventory(page)
</script>