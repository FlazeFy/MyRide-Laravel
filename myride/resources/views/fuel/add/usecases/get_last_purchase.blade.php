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
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="mb-0">${data.fuel_brand} | ${data.fuel_type}</h5>
                                <p class="text-secondary text-dark">Fuel at ${getDateToContext(data.created_at,'calendar')}</p>
                            </div>
                            <div class="d-flex">
                                <h5 class="chip bg-info">RON ${data.fuel_ron}</h5>
                                <h5 class="chip bg-info">${data.fuel_volume} Liter</h5>
                            </div>
                        </div>
                        <h6 class="chip bg-warning d-inline" style="font-size:var(--textXLG);">Rp. ${number_format(data.fuel_price_total, 0, ',', '.')},00</h6>
                    </div>
                `)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status !== 404){
                    generate_api_error(response, true)
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