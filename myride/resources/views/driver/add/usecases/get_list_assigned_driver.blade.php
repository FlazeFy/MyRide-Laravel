<label>Assigned Driver</label>
<table class="table">
    <thead>
        <tr>
            <th scope="col" style="min-width:150px;">Driver</th>
            <th scope="col">Vehicle</th>
        </tr>
    </thead>
    <tbody id="list_assigned_driver-holder">
        <tr><th scope="row" colspan="4" class="fst-italic fw-normal">- No Driver Found -</th></tr>
    </tbody>
</table>

<script>
    let page = 1

    const get_asigned_driver = (page) => {
        const holder = 'list_assigned_driver-holder'

        Swal.showLoading();
        $.ajax({
            url: `/api/v1/driver/vehicle`,
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
                    const vehicleList = dt.vehicle_list.split(', ').map(vh => {
                        const [plate, name] = vh.split('-')
                        return `<li><span class="plate-number">${plate.trim()}</span> ${name.trim()}</li>`
                    });

                    $(`#${holder}`).append(`
                        <tr>
                            <td class="text-start">
                                <h6 class="mb-0">Username</h6>
                                <p>${dt.username}</p>
                                <h6 class="mb-0">Fullname</h6>
                                <p class="mb-0">${dt.fullname}</p>
                            </td>
                            <td class="text-start">
                                <ol class="mb-0">${vehicleList}</ol>
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
                    $(`#${holder}`).html(`<tr><td colspan="5" id="msg-${holder}"></td></tr>`)
                    template_alert_container(`msg-${holder}`, 'no-data', "No driver found", 'add a trip', '<i class="fa-solid fa-gas-pump"></i>','/driver/add')
                }
            }
        });
    };
    get_asigned_driver(page)
</script>