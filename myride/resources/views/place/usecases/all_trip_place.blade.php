<div>
    <img src="{{asset('assets/place.png')}}" alt='place.png' class="img img-fluid w-100 mb-3" style="max-width: 420px;">
    <h2>All Places That You've Visited</h2>
    <table class="table text-center table-bordered">
        <thead>
            <tr>
                <th scope="col" style="min-width: 180px">Place Name</th>
                <th scope="col" style="min-width: 120px">Total</th>
                <th scope="col">Partner</th>
                <th scope="col">Location</th>
            </tr>
        </thead>
        <tbody id="trip_place-holder"></tbody>
    </table>
</div>

<script>
    const getAllPartner = (page) => {
        const holder = 'trip_place-holder'

        $.ajax({
            url: `/api/v1/stats/place`,
            type: 'GET',
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                $(`#${holder}`).empty()
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                
                data.forEach((dt, idx) => {
                    let partnerEl = ''
                    if (dt.partner) dt.partner.forEach(pt => partnerEl += `<div class="chip m-0 text-nowrap mx-auto bg-warning">(${pt.total}) ${pt.context}</div>`)
                    $(`#${holder}`).append(`
                        <tr>
                            <td>${dt.trip_location}</td>
                            <td>
                                <p class="mb-0">Origin: ${dt.total_origin}</p>
                                <p class="mb-0">Destination: ${dt.total_destination}</p>
                            </td>
                            <td>${dt.partner ? `<div class="d-flex flex-wrap gap-2">${partnerEl}</div>`: '-'}</td>
                            <td><a class="btn btn-success" title="See on maps" href="https://www.google.com/maps?q=${dt.trip_coordinate}" target="_blank"><i class="fa-solid fa-location-dot"></i></a></td>
                        </tr>
                    `)
                })
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                response.status !== 404 ? generateApiError(response, true) : templateAlertContainer(holder, 'no-data', "No partner found", null, '<i class="fa-solid fa-rotate-left"></i>',null)
            }
        })
    }
    getAllPartner()
</script>