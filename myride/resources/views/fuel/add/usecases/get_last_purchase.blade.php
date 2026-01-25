<label>Last Purchase</label>
<div id="last_purchase-holder">
    <div class="no-msg-text">- No Fuel Found -</div>
</div>

<script>
    const holder_fuel = 'last_purchase-holder'
    messageAlertBox(holder_fuel, "danger", "You must select a vehicle first")

    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        get_vehicle_last_fuel(id)
    })

    const get_vehicle_last_fuel = (id) => {
        if(id !== "-"){
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
                        <div class="container-fluid bg-primary">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h5 class="mb-0">${data.fuel_brand}${data.fuel_type ? ` | ${data.fuel_type}`:''}</h5>
                                    <p class="text-secondary text-dark mb-0">Fuel at ${getDateToContext(data.created_at,'calendar',false)}</p>
                                </div>
                                <div class="d-flex gap-2">
                                    ${data.fuel_ron ? `<h5 class="chip bg-warning m-0">RON ${data.fuel_ron}</h5>`:''}
                                    <h5 class="chip bg-warning m-0">+ ${data.fuel_volume}${data.fuel_brand !== 'Electric' ? 'L':'%'}</h5>
                                </div>
                            </div>
                            ${
                                data.fuel_price_total ? `<h6 class="chip text-dark bg-warning d-inline mx-0">Rp. ${data.fuel_price_total.toLocaleString()},00</h6>` : `<div class="chip m-0 bg-success d-inline">Free</div>`
                            }
                        </div>
                    `)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status !== 404){
                        generateApiError(response, true)
                    } else {
                        messageAlertBox(holder_fuel, "danger", "You never refuel this vehicle")
                    }
                }
            });
        } else {
            messageAlertBox(holder_fuel, "danger", "You must select a vehicle first")
        }
    }
</script>