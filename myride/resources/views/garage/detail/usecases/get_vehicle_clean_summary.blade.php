<h2 class="mb-3">Clean Summary</h2><hr>
<div id="clean_summary-holder" class="row"></div>

<script>
    const get_clean_summary = () => {
        const holder = 'clean_summary-holder'
        $.ajax({
            url: `/api/v1/clean/summary?vehicle_id=<?= $id ?>`,
            type: 'GET',
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                $(`#${holder}`).append(`
                    <div class="row" style="font-size:var(--textMD);">
                        <div class="col-xl-6 col-lg-12 col-md-6 col-sm-12">
                            <ul class="ps-3">
                                <li>Total Clean : ${data[0].total_clean}</li>
                                <li>Total Spend : Rp.${number_format(data[0].total_price,0,',','.')}</li>
                                <li>Avg Spend per Clean : Rp.${number_format(data[0].avg_price_per_clean,0,',','.')}</li>
                                <li><b>${data[0].total_clean_body} body cleanings</b> out of ${data[0].total_clean} total cleans</li>
                                <li><b>${data[0].total_clean_window} window cleanings</b> out of ${data[0].total_clean} total cleans</li>
                                <li><b>${data[0].total_clean_dashboard} dashboard cleanings</b> out of ${data[0].total_clean} total cleans</li>
                                <li><b>${data[0].total_clean_tires} tire cleanings</b> out of ${data[0].total_clean} total cleans</li>
                                <li><b>${data[0].total_clean_trash} trash removals</b> out of ${data[0].total_clean} total cleans</li>
                            </ul>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-md-6 col-sm-12">
                            <ul class="ps-3">
                                <li><b>${data[0].total_clean_engine} engine cleanings</b> out of ${data[0].total_clean} total cleans</li>
                                <li><b>${data[0].total_clean_seat} seat cleanings</b> out of ${data[0].total_clean} total cleans</li>
                                <li><b>${data[0].total_clean_carpet} carpet cleanings</b> out of ${data[0].total_clean} total cleans</li>
                                <li><b>${data[0].total_clean_pillows} pillow cleanings</b> out of ${data[0].total_clean} total cleans</li>
                                <li><b>${data[0].total_fill_window_cleaning_water} window water refills</b> out of ${data[0].total_clean} total cleans</li>
                                <li><b>${data[0].total_clean_hollow} hollow cleanings</b> out of ${data[0].total_clean} total cleans</li>
                            </ul>
                        </div>
                    </div>
                `)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                if(response.status !== 404){
                    generate_api_error(response, true)
                } else {
                    template_alert_container(holder, 'no-data', "No clean found for this context to generate the summary", 'add a clean', '<i class="fa-solid fa-soap"></i>','/trip/add')
                }
            }
        });
    }
    get_clean_summary()
</script>