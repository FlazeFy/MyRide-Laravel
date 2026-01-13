<h2>Vehicle Service Spending</h2><hr>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col" style="width: 140px;">Vehicle</th>
                <th scope="col">Price Total</th>
            </tr>
        </thead>
        <tbody id="service_spending-holder"></tbody>
    </table>
</div>

<script>
    const get_service_spending = () => {
        const holder = 'service_spending-holder'

        Swal.showLoading()
        $.ajax({
            url: `/api/v1/service/spending`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                $(`#${holder}`).empty()
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                
                data.forEach(dt => {
                    $(`#${holder}`).append(`
                        <tr>
                            <td>
                                <span class="plate-number">${dt.vehicle_plate_number}</span>
                                <p class="text-secondary mt-2 mb-0 fw-bold">${dt.vehicle_type}</p>
                            </td>
                            <td class="text-center">Rp. ${dt.total.toLocaleString()},00</td>
                        </tr>
                    `)
                });
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
    get_service_spending()
</script>