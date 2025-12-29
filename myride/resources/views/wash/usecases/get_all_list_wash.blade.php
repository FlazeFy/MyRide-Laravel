<h2>All Wash</h2><hr>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col" style="min-width: 160px;">Vehicle</th>
                <th scope="col" style="min-width: 280px;">Washing Info</th>
                <th scope="col" style="min-width: 240px;">Washing Detail</th>
                <th scope="col" style="min-width: 160px;">Properties</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody id="wash-holder"></tbody>
    </table>
</div>

<script>
    let page = 1

    const get_all_wash = (page) => {
        const holder = 'wash-holder'

        $.ajax({
            url: `/api/v1/wash`,
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
                                <p class="text-secondary mt-2 fw-bold">${dt.vehicle_type}</p>
                            </td>
                            <td class="text-start">
                                <h6 class="mb-0">Wash By</h6>
                                <p>${dt.wash_by ?? '-'}</p>
                                <h6 class="mb-0">Address</h6>
                                <p>${dt.wash_address ?? '-'}</p>
                                <h6 class="mb-0">Description</h6>
                                <p>${dt.wash_desc ?? '-'}</p>
                                <h6 class="mb-0">Wash Price</h6>
                                ${
                                    dt.wash_price ? `<p>Rp. ${dt.wash_price.toLocaleString()},00</p>` : `<div class="chip-mini bg-success d-inline-block">Free</div>`
                                }
                            </td>
                            <td style="max-width:var(--tcolMinLG);">
                                <div class="row text-start">
                                    ${[
                                        { key: "is_wash_body", label: "Body" },
                                        { key: "is_wash_window", label: "Window" },
                                        { key: "is_wash_dashboard", label: "Dashboard" },
                                        { key: "is_wash_tires", label: "Tires" },
                                        { key: "is_wash_trash", label: "Trash" },
                                        { key: "is_wash_engine", label: "Engine" },
                                        { key: "is_wash_seat", label: "Seat" },
                                        { key: "is_wash_carpet", label: "Carpet" },
                                        { key: "is_wash_pillows", label: "Pillow" },
                                        { key: "is_wash_hollow", label: "Vehicle Hollow" },
                                        { key: "is_fill_window_washing_water", label: "Wiper Water Fill" },
                                    ].map(wash => dt[wash.key] ? `
                                        <div class="col-6"><li class="ms-4">${wash.label}</li></div>`:'').join('')
                                    }
                                </div>
                            </td>
                            <td class="text-start">
                                <h6 class="mb-0">Start At</h6>
                                <p class="mb-0">${getDateToContext(dt.wash_start_time,'calendar')}</p>
                                <h6 class="mb-0">Finished At</h6>
                                <p class="mb-0">${dt.wash_end_time ? getDateToContext(dt.wash_end_time,'calendar') : "In Progress"}</p>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                    <a class="btn btn-danger btn-delete" style="width:50px;" data-url="/api/v1/wash/destroy/${dt.id}" data-context="Wash"><i class="fa-solid fa-trash"></i></a>
                                    ${dt.wash_end_time === null ? `<a class="btn btn-success btn-finish" data-id="${dt.id}"><i class="fa-solid fa-check"></i></a>` : ""}
                                    <a class="btn btn-warning btn-update" style="width:50px;" 
                                        data-vehicle-plate-number="${dt.vehicle_plate_number}" data-id="${dt.id}"
                                        data-wash-by="${dt.wash_by}" data-wash-address="${dt.wash_address}" data-wash-desc="${dt.wash_desc}" data-wash-price="${dt.wash_price}"
                                        data-is-wash-body="${dt.is_wash_body}" data-is-wash-window="${dt.is_wash_window}" data-is-wash-dashboard="${dt.is_wash_dashboard}" 
                                        data-is-wash-tires="${dt.is_wash_tires}" data-is-wash-trash="${dt.is_wash_trash}" data-is-wash-engine="${dt.is_wash_engine}" 
                                        data-is-wash-seat="${dt.is_wash_seat}" data-is-wash-carpet="${dt.is_wash_carpet}" data-is-wash-pillows="${dt.is_wash_pillows}" 
                                        data-is-wash-hollow="${dt.is_wash_hollow}" data-is-fill-window-washing-water="${dt.is_fill_window_washing_water}"
                                        data-wash-start-time="${dt.wash_start_time}" data-wash-end-time="${dt.wash_end_time}"
                                        ><i class="fa-solid fa-pen-to-square"></i></a>
                                </div>
                            </td>
                        </tr>
                    `)
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()

                if(response.status != 404){
                    generateApiError(response, true)
                } else {
                    $(`#${holder}`).html(`<tr><td colspan="5" id="msg-${holder}"></td></tr>`)
                    templateAlertContainer(`msg-${holder}`, 'no-data', "No wash found", 'add a wash history', '<i class="fa-solid fa-soap"></i>','/wash/add')
                }
            }
        });
    };
    get_all_wash(page)
</script>