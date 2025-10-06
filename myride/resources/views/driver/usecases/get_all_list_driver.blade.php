<h2>All Service</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col" style="width: 300px;">Username & FullName</th>
            <th scope="col" style="width: 300px;">Contact</th>
            <th scope="col">Notes</th>
            <th scope="col" style="width: 160px;">Properties</th>
            <th scope="col" style="width: 130px;">Action</th>
        </tr>
    </thead>
    <tbody id="driver-holder"></tbody>
</table>

<script>
    let page = 1

    const get_all_driver = (page) => {
        const holder = 'driver-holder'

        Swal.showLoading();
        $.ajax({
            url: `/api/v1/driver`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
                $(`#${holder}`).empty()
            },
            success: function(response) {
                Swal.close()
                const data = response.data.data
                
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
                            <td class="text-start">${dt.notes}</td>
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
                                <a class="btn btn-danger btn-delete" style="width:50px;" data-url="/api/v1/driver/destroy/${dt.id}" data-context="Driver"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    `)
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status != 404){
                    failedMsg('get the driver')
                } else {
                    $(`#${holder}`).html(`<tr><td colspan="6" id="msg-${holder}"></td></tr>`)
                    template_alert_container(`msg-${holder}`, 'no-data', "No driver found", 'add a driver', '<i class="fa-solid fa-user"></i>','/driver/add')
                }
            }
        });
    };
    get_all_driver(page)
</script>