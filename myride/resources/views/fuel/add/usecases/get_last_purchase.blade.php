<label>Last Purchase</label>
<div id="last_purchase-holder">
    <div class="no-msg-text">- No Fuel Found -</div>
</div>

<script>
    const holder_fuel = 'last_purchase-holder'

    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        if(id === "-"){
            $(`#${holder_fuel}`).html(`<div class="no-msg-text">- No Fuel Found -</div>`)
        }
    })

    const get_vehicle_last_fuel = (id) => {
        Swal.showLoading()
        $.ajax({
            url: `/api/v1/fuel/last?vehicle_id=${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                
                $(`#${holder_fuel}`).html(`
                    <div class="container-fluid bg-success">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h5 class="mb-0">${data.fuel_brand} | ${data.fuel_type}</h5>
                                <p class="text-secondary text-dark mb-0">Fuel at ${getDateToContext(data.created_at,'calendar')}</p>
                            </div>
                            <div class="d-flex gap-2">
                                <h5 class="chip bg-info mx-0 mb-0">RON ${data.fuel_ron}</h5>
                                <h5 class="chip bg-info mx-0 mb-0">${data.fuel_volume} Liter</h5>
                            </div>
                        </div>
                        <h6 class="chip bg-warning d-inline mx-0" style="font-size:var(--textXLG);">Rp. ${data.fuel_price_total.toLocaleString()},00</h6>
                    </div>
                `)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status !== 404){
                    generateApiError(response, true)
                } else {
                    $(`#${holder_fuel}`).html(`
                        <div class="container-fluid bg-danger">
                            <h6><i class="fa-solid fa-triangle-exclamation"></i> Alert</h6>
                            <p class="mb-0">You never by a fuel with this vehicle</p>
                        </div>
                    `)
                }
            }
        });
    }
</script>