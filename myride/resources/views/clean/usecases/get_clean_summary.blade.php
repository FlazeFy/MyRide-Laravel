<h2>Clean Summary</h2><hr>
<div id="clean_summary-holder" class="row"></div>

<script>
    const get_clean_summary = () => {
        Swal.showLoading()
        const ctx = 'summary_clean'

        const generate_summary = (data) => {
            data.forEach((dt,idx) => {
                $('#clean_summary-holder').append(`
                    <div class="col-lg-12 col-md-6 col-sm-12">
                        <button class="btn btn-primary w-100 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#clean_${dt.vehicle_plate_number.replaceAll(' ','_')}_summary-collapse" aria-expanded="false" aria-controls="collapseExample">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="plate-number m-0">${dt.vehicle_plate_number}</span>
                                <p class="text-white mb-0 fw-bold">${dt.vehicle_type}</p>
                            </div>
                            <p class="text-white mt-2 mb-0">${dt.vehicle_name}</p>
                        </button>
                        <div class="collapse mt-2" style='font-size: var(--textMD)' id='clean_${dt.vehicle_plate_number.replaceAll(' ','_')}_summary-collapse'>
                            <ul class="ps-3">
                                <li>Total Clean : ${dt.total_clean}</li>
                                <li>Total Spend : Rp.${number_format(dt.total_price,0,',','.')}</li>
                                <li>Avg Spend per Clean : Rp.${number_format(dt.avg_price_per_clean,0,',','.')}</li>
                                <li><b>${dt.total_clean_body} body cleanings</b> out of ${dt.total_clean} total cleans</li>
                                <li><b>${dt.total_clean_window} window cleanings</b> out of ${dt.total_clean} total cleans</li>
                                <li><b>${dt.total_clean_dashboard} dashboard cleanings</b> out of ${dt.total_clean} total cleans</li>
                                <li><b>${dt.total_clean_tires} tire cleanings</b> out of ${dt.total_clean} total cleans</li>
                                <li><b>${dt.total_clean_trash} trash removals</b> out of ${dt.total_clean} total cleans</li>
                                <li><b>${dt.total_clean_engine} engine cleanings</b> out of ${dt.total_clean} total cleans</li>
                                <li><b>${dt.total_clean_seat} seat cleanings</b> out of ${dt.total_clean} total cleans</li>
                                <li><b>${dt.total_clean_carpet} carpet cleanings</b> out of ${dt.total_clean} total cleans</li>
                                <li><b>${dt.total_clean_pillows} pillow cleanings</b> out of ${dt.total_clean} total cleans</li>
                                <li><b>${dt.total_fill_window_cleaning_water} window water refills</b> out of ${dt.total_clean} total cleans</li>
                                <li><b>${dt.total_clean_hollow} hollow cleanings</b> out of ${dt.total_clean} total cleans</li>
                            </ul>
                        </div>
                    </div>
                `)
            })
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/clean/summary`,
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
    get_clean_summary()
</script>