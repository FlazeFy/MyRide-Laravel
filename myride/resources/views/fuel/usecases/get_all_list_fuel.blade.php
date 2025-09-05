<h2>All Fuel</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col" style="width: 140px;">Vehicle</th>
            <th scope="col" style="width: 240px;">Fuel Info</th>
            <th scope="col">Type / Brand</th>
            <th scope="col" style="width: 160px;">Fuel At</th>
            <th scope="col" style="width: 130px;">Action</th>
        </tr>
    </thead>
    <tbody id="fuel-holder"></tbody>
</table>

<script>
    let page = 1

    const get_all_fuel = (page) => {
        const holder = $('#fuel-holder')

        Swal.showLoading();
        $.ajax({
            url: `/api/v1/fuel`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
                holder.empty()
            },
            success: function(response) {
                Swal.close()
                const data = response.data.data
                
                data.forEach(dt => {
                    holder.append(`
                        <tr>
                            <td>
                                <span class="plate-number">${dt.vehicle_plate_number}</span>
                                <p class="text-secondary mt-2 mb-0 fw-bold">${dt.vehicle_type}</p>
                            </td>
                            <td class="text-start">
                                <h6 class="mb-0">Volume</h6>
                                <p class="mb-0">${dt.fuel_volume}${dt.fuel_brand == 'Electric' ? '%' : ' L'}</p>
                                <h6 class="mb-0">Price Total</h6>
                                <p>Rp. ${number_format(dt.fuel_price_total, 0, ',', '.')},00</p>
                            </td>
                            <td class="text-start">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-0">Brand</h6>
                                        <p class="mb-0">${dt.fuel_brand}</p>
                                    </div>
                                    ${
                                        dt.fuel_brand != 'Electric' ?  
                                            `<div>
                                                <h6 class="mb-0">RON</h6>
                                                <p class="mb-0">${dt.fuel_ron}</p>
                                            </div>`
                                        :''
                                    }
                                </div>
                                ${
                                    dt.fuel_brand != 'Electric' ?  
                                        `<h6 class="mb-0">Type</h6>
                                        <p class="mb-0">${dt.fuel_type}</p>`
                                    :''
                                }
                            </td>
                            <td class="text-start">
                                <p class="mb-0">${getDateToContext(dt.created_at,'calendar')}</p>
                            </td>
                            <td>
                                ${dt.fuel_bill != null ? `<a class="btn btn-primary" style="width:50px;"><i class="fa-solid fa-receipt"></i></a>` : ""}
                                <a class="btn btn-danger" style="width:50px;"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    `)
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                Swal.fire({
                    title: "Oops!",
                    text: "Something went wrong",
                    icon: "error"
                });
            }
        });
    };
    get_all_fuel(page)
</script>