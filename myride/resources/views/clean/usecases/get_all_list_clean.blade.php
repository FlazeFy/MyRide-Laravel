<h2>All Clean</h2>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col" style="min-width: 160px;">Vehicle</th>
                <th scope="col" style="min-width: 200px;">Cleaning Info</th>
                <th scope="col" style="min-width: 240px;">Cleaning Detail</th>
                <th scope="col" style="min-width: 160px;">Properties</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody id="clean-holder"></tbody>
    </table>
</div>

<script>
    let page = 1

    const get_all_clean = (page) => {
        const holder = 'clean-holder'

        $.ajax({
            url: `/api/v1/clean`,
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
                                <h6 class="mb-0">Clean By</h6>
                                <p>${dt.clean_by ?? '-'}</p>
                                <h6 class="mb-0">Address</h6>
                                <p>${dt.clean_address ?? '-'}</p>
                                <h6 class="mb-0">Description</h6>
                                <p>${dt.clean_desc ?? '-'}</p>
                                <h6 class="mb-0">Tools</h6>
                                <p>${dt.clean_tools ?? '-'}</p>
                                <h6 class="mb-0">Clean Price</h6>
                                <p>${`Rp. ${number_format(dt.clean_price, 0, ',', '.')},00` ?? '-'}</p>
                            </td>
                            <td style="max-width:var(--tcolMinLG);">
                                <div class="row text-start">
                                    ${[
                                        { key: "is_clean_body", label: "Body" },
                                        { key: "is_clean_window", label: "Window" },
                                        { key: "is_clean_dashboard", label: "Dashboard" },
                                        { key: "is_clean_tires", label: "Tires" },
                                        { key: "is_clean_trash", label: "Trash" },
                                        { key: "is_clean_engine", label: "Engine" },
                                        { key: "is_clean_seat", label: "Seat" },
                                        { key: "is_clean_carpet", label: "Carpet" },
                                        { key: "is_clean_pillows", label: "Pillow" },
                                        { key: "is_clean_hollow", label: "Vehicle Hollow" },
                                        { key: "is_fill_window_cleaning_water", label: "Wiper Water Fill" },
                                    ].map(clean => `
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" ${dt[clean.key] ? 'checked' : ''}>
                                                <label class="form-check-label">${clean.label}</label>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </td>
                            <td class="text-start">
                                <h6 class="mb-0">Start At</h6>
                                <p class="mb-0">${getDateToContext(dt.clean_start_time,'calendar')}</p>
                                <h6 class="mb-0">Finished At</h6>
                                <p class="mb-0">${dt.clean_end_time ? getDateToContext(dt.clean_end_time,'calendar') : "In Progress"}</p>
                            </td>
                            <td>
                                <a class="btn btn-danger w-100 btn-delete" data-url="/api/v1/clean/destroy/${dt.id}" data-context="Clean"><i class="fa-solid fa-trash"></i></a>
                                ${dt.clean_end_time === null ? `<a class="btn btn-success w-100 mt-2 btn-finish" data-id="${dt.id}"><i class="fa-solid fa-check"></i></a>` : ""}
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
                    template_alert_container(`msg-${holder}`, 'no-data', "No clean found", 'add a clean history', '<i class="fa-solid fa-soap"></i>','/clean/add')
                }
            }
        });
    };
    get_all_clean(page)
</script>