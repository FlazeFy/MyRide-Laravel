<label>Service History</label>
<table class="table">
    <thead>
        <tr>
            <th scope="col">Notes</th>
            <th scope="col">Category</th>
            <th scope="col">Info</th>
        </tr>
    </thead>
    <tbody id="list_service_history">
        <tr><th scope="row" colspan="4" class="fst-italic fw-normal">- No Service Found -</th></tr>
    </tbody>
</table>

<script>
    const get_vehicle_service_history = (id) => {
        const holder = 'list_service_history'
        $(`#${holder}`).empty()
        Swal.showLoading()

        $.ajax({
            url: `/api/v1/service/vehicle/${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                
                data.forEach(dt => {
                    $(`#${holder}`).append(`
                        <tr>
                            <td scope="col">${dt.service_note}</td>
                            <td scope="col" style="width:120px;">${dt.service_category}</td>
                            <td scope="col" class="text-start" style="width:150px;">
                                <h6>Price</h6>
                                <p>Rp. ${number_format(dt.service_price_total, 0, ',', '.')},00</p>
                                <h6>Location</h6>
                                <p>${dt.service_location}</p>
                            </td>
                        </tr>
                    `)
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status !== 404){
                    failedMsg('get the service')
                } else {
                    $(`#${holder}`).html(`<th scope="row" colspan="3" class="no-msg-text">- No Service Found -</th>`)
                }
            }
        });
    }
</script>