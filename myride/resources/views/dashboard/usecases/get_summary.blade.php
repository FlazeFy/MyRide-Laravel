<h2>Summary</h2><hr>
<div class="row">
    <div class="col-xl-2 col-lg-3 col-md-4 col-6 mx-auto">
        <h3 class="summary-number" id="total_vehicle-holder">0</h3>
        <h6 class="summary-label">Vehicle</h6>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-6 mx-auto">
        <h3 class="summary-number" id="total_service-holder">0</h3>
        <h6 class="summary-label">Service</h6>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-6 mx-auto">
        <h3 class="summary-number" id="total_wash-holder">0</h3>
        <h6 class="summary-label">Wash</h6>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-6 mx-auto">
        <h3 class="summary-number" id="total_driver-holder">0</h3>
        <h6 class="summary-label">Driver</h6>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-6 mx-auto">
        <h3 class="summary-number" id="total_trip-holder">0</h3>
        <h6 class="summary-label">Trip</h6>
    </div>
</div>

<script>
    const get_summary = () => {
        Swal.showLoading()
        const ctx = 'summary_apps_private'

        const generate_summary = (total_vehicle, total_service, total_wash, total_driver, total_trip) => {
            $('#total_vehicle-holder').text(total_vehicle)
            $('#total_service-holder').text(total_service)
            $('#total_wash-holder').text(total_wash)
            $('#total_driver-holder').text(total_driver)
            $('#total_trip-holder').text(total_trip)
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/stats/summary`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    localStorage.setItem(ctx,JSON.stringify(data))
                    localStorage.setItem(`last-hit-${ctx}`,Date.now())
                    generate_summary(data.total_vehicle, data.total_service, data.total_wash, data.total_driver, data.total_trip)
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
                    generate_summary(data.total_vehicle, data.total_service, data.total_wash, data.total_driver, data.total_trip)
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