<h2>Summary</h2>
<div class="row">
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-xs-12 mx-auto">
        <h1 class="summary-number" id="total_vehicle-holder">0</h1>
        <h4 class="summary-label">Vehicle</h4>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-xs-12 mx-auto">
        <h1 class="summary-number" id="total_service-holder">0</h1>
        <h4 class="summary-label">Service</h4>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-xs-12 mx-auto">
        <h1 class="summary-number" id="total_clean-holder">0</h1>
        <h4 class="summary-label">Clean</h4>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-xs-12 mx-auto">
        <h1 class="summary-number" id="total_driver-holder">0</h1>
        <h4 class="summary-label">Driver</h4>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-xs-12 mx-auto">
        <h1 class="summary-number" id="total_trip-holder">0</h1>
        <h4 class="summary-label">Trip</h4>
    </div>
</div>

<script>
    const get_summary = () => {
        Swal.showLoading()
        const ctx = 'summary_apps_private'

        const generate_summary = (total_vehicle, total_service, total_clean, total_driver, total_trip) => {
            $('#total_vehicle-holder').text(total_vehicle)
            $('#total_service-holder').text(total_service)
            $('#total_clean-holder').text(total_clean)
            $('#total_driver-holder').text(total_driver)
            $('#total_trip-holder').text(total_trip)
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/stats/summary`,
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
                    generate_summary(data.total_vehicle, data.total_service, data.total_clean, data.total_driver, data.total_trip)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    failedMsg('get the summary')
                }
            });
        }

        if(ctx in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < summaryFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx))
                if(data){
                    generate_summary(data.total_vehicle, data.total_service, data.total_clean, data.total_driver, data.total_trip)
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