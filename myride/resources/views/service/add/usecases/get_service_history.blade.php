<label>Service History</label>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th scope="col" style="min-width: 260px">Notes</th>
                <th scope="col" style="min-width: 140px">Category</th>
                <th scope="col" style="min-width: 160px">Info</th>
                <th scope="col" style="min-width: 160px">Props</th>
            </tr>
        </thead>
        <tbody id="list_service_history">
            <tr><th scope="row" colspan="4" class="fst-italic fw-normal">- No Service Found -</th></tr>
        </tbody>
    </table>
</div>

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
                            <td scope="col">${dt.service_category}</td>
                            <td scope="col" class="text-start">
                                <h6 class="mb-0">Price</h6>
                                <p>Rp. ${dt.service_price_total.toLocaleString()},00</p>
                                <h6 class="mb-0">Location</h6>
                                <p class="mb-0">${dt.service_location}</p>
                            </td>
                            <td scope="col" class="text-start">
                                <h6 class="mb-0">Created At</h6>
                                <p>${getDateToContext(dt.created_at,'calendar')}</p>
                                ${
                                    dt.remind_at ? `
                                        <h6 class="mb-0">Remind At</h6>
                                        <p class="mb-0">${getDateToContext(dt.remind_at,'calendar')}</p>
                                    ` : ''
                                }
                            </td>
                        </tr>
                    `)
                })
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status !== 404){
                    generateApiError(response, true)
                } else {
                    $(`#${holder}`).html(`<tr><td scope="row" colspan="3" class="no-msg-text">- No Service Found -</td></tr>`)
                }
            }
        })
    }
</script>