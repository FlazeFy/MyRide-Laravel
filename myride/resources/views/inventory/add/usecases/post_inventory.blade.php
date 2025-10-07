<h2>Add Inventory</h2>
<form id="form-add-inventory">
    <div class="row">
        <div class="col-xl-6 col-lg-12 pb-4">
            <div class="row">
                <div class="col-12">
                    <label>Vehicle Name & Plate Number</label>
                    <select class="form-select" name="vehicle_holder" id="vehicle_holder" aria-label="Default select example">
                        <option>-</option>
                    </select>
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
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Inventory Category</label>
                    <select class="form-select" name="inventory_category" id="inventory_category_holder" aria-label="Default select example">
                        <option>-</option>
                    </select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Inventory Storage</label>
                    <select class="form-select" name="inventory_storage" id="inventory_storage_holder" aria-label="Default select example">
                        <option>-</option>
                    </select>
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
        get_vehicle_attached_inventory(id)
    })
    $(document).on('change','#inventory_category_holder', function(){
        const val = $(this).val()
    })

    const get_vehicle_name_opt = () => {
        Swal.showLoading()
        const ctx = 'vehicle_name_temp'
        const ctx_holder = 'vehicle_holder'

        const generate_vehicle_list = (holder,data) => {
            data.forEach(el => {
                $(`#${holder}`).append(`<option value="${el.id}">${el.vehicle_plate_number} - ${el.vehicle_name}</option>`)
            });
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/vehicle/name`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    localStorage.setItem(ctx,JSON.stringify(data))
                    localStorage.setItem(`last-hit-${ctx}`,Date.now())
                    generate_vehicle_list(ctx_holder,data)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 404){
                        failedMsg(`get the vehicle list`)
                    } else {
                        // .....
                    }
                }
            });
        }

        if(ctx in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < statsFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx))
                if(data){
                    generate_vehicle_list(ctx_holder,data)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg(`get the vehicle list`)
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }

    const get_context_opt = (context) => {
        Swal.showLoading()
        const ctx = `inventory_temp`
        let ctx_holder

        if(context.includes(',')){
            ctx_holder = []
            context = context.split(',')
            context.forEach(el => {
                ctx_holder.push(`${el}_holder`)
            })
        } else {
            ctx_holder = `${context}_holder`
        }

        const generate_context_list = (holder,data) => {
            if(Array.isArray(holder)){
                holder.forEach(dt => {
                    $(`#${dt}`).empty().append(`<option>-</option>`)
                    data.forEach(el => {
                        el.dictionary_type === dt.replace('_holder','') && $(`#${dt}`).append(`<option value="${el.dictionary_name}">${el.dictionary_name}</option>`)
                    });
                });
            } else {
                $(`#${holder}`).empty().append(`<option>-</option>`)
                data.forEach(el => {
                    $(`#${holder}`).append(`<option value="${el.dictionary_name}">${el.dictionary_name}</option>`)
                });
            }
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/dictionary/type/${context}`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    localStorage.setItem(ctx,JSON.stringify(data))
                    localStorage.setItem(`last-hit-${ctx}`,Date.now())
                    generate_context_list(ctx_holder,data)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 404){
                        failedMsg(`get the ${context} list`)
                    } else {
                        // .....
                    }
                }
            });
        }

        if(ctx in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < statsFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx))
                if(data){
                    generate_context_list(ctx_holder,data)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg(`get the ${context} list`)
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }

    get_vehicle_name_opt()
    get_context_opt('inventory_category,inventory_storage')

    const get_vehicle_detail = (id) => {
        Swal.showLoading();
        $.ajax({
            url: `/api/v1/vehicle/detail/${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                $('#vehicle_type').val(data.vehicle_type)
                $('#vehicle_category').val(data.vehicle_category)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                failedMsg('get the vehicle')
            }
        });
    }

    const get_vehicle_attached_inventory = (id) => {
        const holder = 'list_attached_inventory-holder'
        $(`#${holder}`).empty()
        Swal.showLoading()

        $.ajax({
            url: `/api/v1/inventory/vehicle/${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
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
                if(response.status != 404){
                    $(`#${holder}`).html(`<th scope="row" colspan="4" class="fst-italic fw-normal">- No Inventory Found -</th>`)
                } else {
                    failedMsg('get the vehicle last fuel')
                }
            }
        });
    }

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