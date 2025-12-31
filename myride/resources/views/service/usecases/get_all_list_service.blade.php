<h2>All Service</h2><hr>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col" style="min-width: 160px;">Vehicle</th>
                <th scope="col" style="min-width: 160px;">Category</th>
                <th scope="col" style="min-width: 160px;">Info</th>
                <th scope="col" style="min-width: 200px;">Notes</th>
                <th scope="col" style="min-width: 160px;">Properties</th>
                <th scope="col" style="width: 130px;">Action</th>
            </tr>
        </thead>
        <tbody id="service-holder"></tbody>
    </table>
</div>

<script>
    let page = 1

    const getAllService = (page) => {
        const holder = 'service-holder'

        $.ajax({
            url: `/api/v1/service?page=${page}`,
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
                const current_page = response.data.current_page
                const total_page = response.data.last_page
                
                data.forEach(dt => {
                    $(`#${holder}`).append(`
                        <tr>
                            <td>
                                <span class="plate-number">${dt.vehicle_plate_number}</span>
                                <p class="text-secondary mt-2 mb-0 fw-bold">${dt.vehicle_type}</p>
                            </td>
                            <td class="text-center">${dt.service_category}</td>
                            <td class="text-start">
                                <h6 class="mb-0">Location</h6>
                                <p class="mb-0">${dt.service_location}</p>
                                <h6 class="mb-0">Price Total</h6>
                                <p class="mb-0">Rp. ${dt.service_price_total.toLocaleString()},00</p>
                            </td>
                            <td class="text-start">${dt.service_note}</td>
                            <td class="text-start">
                                <h6 class="mb-0">Created At</h6>
                                <p class="mb-0">${getDateToContext(dt.created_at,'calendar')}</p>
                                ${
                                    dt.updated_at ? `
                                        <h6 class="mb-0">Updated At</h6>
                                        <p class="mb-0">${getDateToContext(dt.updated_at,'calendar')}</p>
                                    ` : ''
                                }
                                ${
                                    dt.remind_at ? `
                                        <h6 class="mb-0">Remind At</h6>
                                        <p class="mb-0">${getDateToContext(dt.remind_at,'calendar')}</p>
                                    ` : ''
                                }
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-danger btn-delete" style="width:50px;" data-url="/api/v1/service/destroy/${dt.id}" data-context="Service"><i class="fa-solid fa-trash"></i></a>
                                    <a class="btn btn-warning btn-update" style="width:50px;" 
                                        data-vehicle-plate-number="${dt.vehicle_plate_number}" data-id="${dt.id}" data-remind-at="${dt.remind_at}"
                                        data-service-category="${dt.service_category}" data-service-price-total="${dt.service_price_total}" 
                                        data-service-note="${dt.service_note}" data-service-location="${dt.service_location}"><i class="fa-solid fa-pen-to-square"></i></a>
                                </div>
                            </td>
                        </tr>
                    `)
                });

                generatePagination(holder, getAllService, total_page, current_page)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status != 404){
                    generateApiError(response, true)
                } else {
                    $(`#${holder}`).html(`<tr><td colspan="6" id="msg-${holder}"></td></tr>`)
                    templateAlertContainer(`msg-${holder}`, 'no-data', "No service found", 'add a service', '<i class="fa-solid fa-screwdriver-wrench"></i>','/service/add')
                }
            }
        });
    };
    getAllService(page)
</script>