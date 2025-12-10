<h2>All Fuel</h2><hr>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col" style="min-width: 160px;">Vehicle</th>
                <th scope="col" style="min-width: 160px;">Fuel Info</th>
                <th scope="col" style="min-width: 160px;">Type / Brand</th>
                <th scope="col" style="min-width: 160px;">Fuel At</th>
                <th scope="col" style="width: 130px;">Action</th>
            </tr>
        </thead>
        <tbody id="fuel-holder"></tbody>
    </table>
</div>

<script>
    let page = 1

    const get_all_fuel = (page) => {
        const holder = 'fuel-holder'

        $.ajax({
            url: `/api/v1/fuel`,
            type: 'GET',
            beforeSend: function (xhr) {
                Swal.showLoading()
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
                            <td>${getDateToContext(dt.created_at,'calendar')}</td>
                            <td>
                                <div class='d-flex flex-wrap gap-2'>
                                    ${dt.fuel_bill != null ? `
                                        <a class="btn btn-primary" style="width:50px;" data-bs-target="#fuel_bill_${dt.id}-modal" data-bs-toggle="modal"><i class="fa-solid fa-receipt"></i></a>
                                        <div class="modal fade" id="fuel_bill_${dt.id}-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Fuel Bill</h5>
                                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <img class="img img-fluid" src="${dt.fuel_bill}" alt="${dt.fuel_bill}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ` : ""}
                                    <a class="btn btn-danger btn-delete" style="width:50px;" data-url="/api/v1/fuel/destroy/${dt.id}" data-context="Fuel"><i class="fa-solid fa-trash"></i></a>
                                    <a class="btn btn-warning btn-update" style="width:50px;" data-vehicle-plate-number="${dt.vehicle_plate_number}" data-id="${dt.id}"
                                        data-fuel-type="${dt.fuel_type}" data-fuel-brand="${dt.fuel_brand}" data-fuel-volume="${dt.fuel_volume}" data-fuel-price-total="${dt.fuel_price_total}" data-fuel-ron="${dt.fuel_ron}"><i class="fa-solid fa-pen-to-square"></i></a>
                                </div>
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
                    $(`#${holder}`).html(`<tr><td colspan="5" id="msg-${holder}"></td></tr>`)
                    template_alert_container(`msg-${holder}`, 'no-data', "No fuel found", 'add a fuel', '<i class="fa-solid fa-gas-pump"></i>','/fuel/add')
                }
            }
        });
    };
    get_all_fuel(page)
</script>