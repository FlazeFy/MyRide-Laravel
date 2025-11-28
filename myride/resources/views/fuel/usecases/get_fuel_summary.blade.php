<h2>Summary</h2>
<div class="row">
    <div class="col-md-6 col-6">
        <h5 class="summary-number" id="total_fuel_volume-holder">0L</h5>
        <h6 class="summary-label">Consumed</h6>
    </div>
    <div class="col-md-6 col-6 text-end">
        <h5 class="summary-number" id="total_refueling-holder">0</h5>
        <h6 class="summary-label">Refueling</h6>
    </div>
    <div class="col-12 col-6 text-center">
        <h5 class="summary-number" style="font-size:calc(var(--textJumbo)*1.5);" id="total_fuel_price-holder">Rp. 0</h5>
        <h6 class="summary-label">Spending</h6>
    </div>
</div>

<script>
    const get_summary = () => {
        Swal.showLoading()
        const ctx = 'summary_fuel'

        const generate_summary = (total_fuel_price, total_fuel_volume, total_refueling) => {
            $('#total_fuel_price-holder').text(`Rp. ${number_format(total_fuel_price, 0, ',', '.')},00`)
            $('#total_fuel_volume-holder').text(`${total_fuel_volume}L`)
            $('#total_refueling-holder').text(total_refueling)
        }

        const fetchData = () => {
            const month_year = '09-2025'
            $.ajax({
                url: `/api/v1/fuel/summary/${month_year}`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    localStorage.setItem(ctx,JSON.stringify(data))
                    localStorage.setItem(`last-hit-${ctx}`,Date.now())
                    generate_summary(data.total_fuel_price, data.total_fuel_volume, data.total_refueling)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    generate_api_error(response, true)
                }
            });
        }

        if(ctx in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < summaryFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx))
                if(data){
                    generate_summary(data.total_fuel_price, data.total_fuel_volume, data.total_refueling)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg('get the summary')
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }
    get_summary()
</script>