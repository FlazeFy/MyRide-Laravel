<label>Assigned Driver</label>
<table class="table">
    <thead>
        <tr>
            <th scope="col" style="min-width:150px;">Driver</th>
            <th scope="col">Vehicle</th>
        </tr>
    </thead>
    <tbody id="list_assigned_driver-holder"></tbody>
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
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                $(`#${holder}`).empty()
            },
            success: function(response) {
                Swal.close()
                const data = response.data.data
                
                data.forEach(dt => {
                    const vehicleList = dt.vehicle_list ? dt.vehicle_list.split(', ').map(vh => {
                        const [plate, name] = vh.split('-')
                        return `<li><span class="plate-number">${plate.trim()}</span> ${name.trim()}</li>`
                    }) : '<span class="no-msg-text">- No Vehicle Assigned -</span>';

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
                    generate_api_error(response, true)
                } else {
                    $(`#${holder}`).html(`<tr><td colspan="5" id="msg-${holder}" class="no-msg-text">- No Driver Found -</td></tr>`)
                }
            }
        });
    };
    get_asigned_driver(page)
</script>