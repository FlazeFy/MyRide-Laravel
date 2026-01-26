<h2>Wash Summary</h2><hr>
<div id="wash_summary-holder" class="row"></div>

<script>
    const get_wash_summary = () => {
        Swal.showLoading()
        const ctx = 'summary_wash'
        const ctx_holder = 'wash_summary-holder'

        const generate_summary = (data) => {
            data.forEach((dt,idx) => {
                $(`#${ctx_holder}`).append(`
                    <div class="col-lg-12 col-md-6 col-sm-12">
                        <button class="btn btn-primary w-100 text-start mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#wash_${dt.vehicle_plate_number.replaceAll(' ','_')}_summary-collapse" aria-expanded="false" aria-controls="collapseExample">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="plate-number m-0">${dt.vehicle_plate_number}</span>
                                <p class="text-white mb-0 fw-bold">${dt.vehicle_type}</p>
                            </div>
                            <p class="text-white mt-2 mb-0">${dt.vehicle_name}</p>
                        </button>
                        <div class="collapse mt-2" style='font-size: var(--textMD)' id='wash_${dt.vehicle_plate_number.replaceAll(' ','_')}_summary-collapse'>
                            <ul class="ps-3">
                                <li>Total Wash : ${dt.total_wash}</li>
                                <li>Total Spend : Rp.${dt.total_price ? dt.total_price.toLocaleString() : '0'}</li>
                                <li>Avg Spend per Wash : Rp.${dt.total_price ? dt.avg_price_per_wash.toLocaleString():'0'}</li>
                                <li><b>${dt.total_wash_body} body washings</b> out of ${dt.total_wash} total washs</li>
                                <li><b>${dt.total_wash_window} window washings</b> out of ${dt.total_wash} total washs</li>
                                <li><b>${dt.total_wash_dashboard} dashboard washings</b> out of ${dt.total_wash} total washs</li>
                                <li><b>${dt.total_wash_tires} tire washings</b> out of ${dt.total_wash} total washs</li>
                                <li><b>${dt.total_wash_trash} trash removals</b> out of ${dt.total_wash} total washs</li>
                                <li><b>${dt.total_wash_engine} engine washings</b> out of ${dt.total_wash} total washs</li>
                                <li><b>${dt.total_wash_seat} seat washings</b> out of ${dt.total_wash} total washs</li>
                                <li><b>${dt.total_wash_carpet} carpet washings</b> out of ${dt.total_wash} total washs</li>
                                <li><b>${dt.total_wash_pillows} pillow washings</b> out of ${dt.total_wash} total washs</li>
                                <li><b>${dt.total_fill_window_washing_water} window water refills</b> out of ${dt.total_wash} total washs</li>
                                <li><b>${dt.total_wash_hollow} hollow washings</b> out of ${dt.total_wash} total washs</li>
                            </ul>
                        </div>
                    </div>
                `)
            })
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/wash/summary`,
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
                    generate_summary(data)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 404){
                        generateApiError(response, true)
                    } else {
                        templateAlertContainer(ctx_holder, 'no-data', "No wash found for this context to generate the stats", 'add a wash', '<i class="fa-solid fa-soap"></i>','/wash/add')
                    }
                }
            })
        }

        if(ctx in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < summaryFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx))
                if(data){
                    generate_summary(data)
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
    get_wash_summary()
</script>