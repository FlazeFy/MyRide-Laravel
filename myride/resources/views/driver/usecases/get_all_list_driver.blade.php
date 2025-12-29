<h2>All Driver</h2><hr>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col" style="min-width: 240px;">Username & FullName</th>
                <th scope="col" style="min-width: 240px;">Contact</th>
                <th scope="col" style="min-width: 240px;">Notes</th>
                <th scope="col" style="min-width: 140px;">Total Trip</th>
                <th scope="col" style="min-width: 140px;">Properties</th>
                <th scope="col" style="min-width: 140px;">Action</th>
            </tr>
        </thead>
        <tbody id="driver-holder"></tbody>
    </table>
</div>

<script>
    let page = 1

    const get_all_driver = (page) => {
        const holder = 'driver-holder'

        Swal.showLoading();
        $.ajax({
            url: `/api/v1/driver?page=${page}`,
            type: 'GET',
            beforeSend: function (xhr) {
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
                                <span class="plate-number"><i class="fa-solid fa-user-tie"></i> ${dt.username}</span>
                                <p class="text-secondary mt-2 mb-0 fw-bold">${dt.fullname}</p>
                            </td>
                            <td class="text-start">
                                <h6 class="mb-0">Email</h6>
                                <p class="mb-0">${dt.email}</p>
                                <h6 class="mb-0">Phone Number</h6>
                                <p class="mb-0">${dt.phone ?? '-'}</p>
                            </td>
                            <td class="text-start">${dt.notes ?? '-'}</td>
                            <td>${dt.total_trip}</td>
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
                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                    <a class="btn btn-danger btn-delete" style="width:50px;" data-url="/api/v1/driver/destroy/${dt.id}" data-context="Driver"><i class="fa-solid fa-trash"></i></a>
                                    <a class="btn btn-warning btn-update" style="width:50px;" 
                                        data-username="${dt.username}" data-id="${dt.id}" 
                                        data-fullname="${dt.fullname}" data-email="${dt.email}" 
                                        data-notes="${dt.notes}" data-phone="${dt.phone}"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a class="btn btn-primary btn-history-trip" style="width:50px;" data-bs-toggle="modal" data-bs-target="#trip_history-modal" 
                                        data-username="${dt.username}" data-id="${dt.id}"><i class="fa-solid fa-rotate-left"></i></a>
                                </div>
                            </td>
                        </tr>
                    `)
                });

                generatePagination(holder, get_all_driver, total_page, current_page)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status != 404){
                    generateApiError(response, true)
                } else {
                    $(`#${holder}`).html(`<tr><td colspan="6" id="msg-${holder}"></td></tr>`)
                    templateAlertContainer(`msg-${holder}`, 'no-data', "No driver found", 'add a driver', '<i class="fa-solid fa-user"></i>','/driver/add')
                }
            }
        });
    };
    get_all_driver(page)
</script>