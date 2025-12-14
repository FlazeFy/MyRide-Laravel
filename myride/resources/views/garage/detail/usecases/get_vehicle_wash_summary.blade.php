<h2 class="mb-3">Wash Summary</h2><hr>
<div id="wash_summary-holder" class="row"></div>

<script>
    const get_wash_summary = () => {
        const holder = 'wash_summary-holder'
        $.ajax({
            url: `/api/v1/wash/summary?vehicle_id=<?= $id ?>`,
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
                                <li>Total Wash : ${data[0].total_wash}</li>
                                <li>Total Spend : Rp.${numberFormat(data[0].total_price,0,',','.')}</li>
                                <li>Avg Spend per Wash : Rp.${numberFormat(data[0].avg_price_per_wash,0,',','.')}</li>
                                <li><b>${data[0].total_wash_body} body washings</b> out of ${data[0].total_wash} total washs</li>
                                <li><b>${data[0].total_wash_window} window washings</b> out of ${data[0].total_wash} total washs</li>
                                <li><b>${data[0].total_wash_dashboard} dashboard washings</b> out of ${data[0].total_wash} total washs</li>
                                <li><b>${data[0].total_wash_tires} tire washings</b> out of ${data[0].total_wash} total washs</li>
                                <li><b>${data[0].total_wash_trash} trash removals</b> out of ${data[0].total_wash} total washs</li>
                            </ul>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-md-6 col-sm-12">
                            <ul class="ps-3">
                                <li><b>${data[0].total_wash_engine} engine washings</b> out of ${data[0].total_wash} total washs</li>
                                <li><b>${data[0].total_wash_seat} seat washings</b> out of ${data[0].total_wash} total washs</li>
                                <li><b>${data[0].total_wash_carpet} carpet washings</b> out of ${data[0].total_wash} total washs</li>
                                <li><b>${data[0].total_wash_pillows} pillow washings</b> out of ${data[0].total_wash} total washs</li>
                                <li><b>${data[0].total_fill_window_washing_water} window water refills</b> out of ${data[0].total_wash} total washs</li>
                                <li><b>${data[0].total_wash_hollow} hollow washings</b> out of ${data[0].total_wash} total washs</li>
                            </ul>
                        </div>
                    </div>
                `)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                if(response.status !== 404){
                    generateApiError(response, true)
                } else {
                    templateAlertContainer(holder, 'no-data', "No wash found for this context to generate the summary", 'add a wash', '<i class="fa-solid fa-soap"></i>','/trip/add')
                }
            }
        });
    }
    get_wash_summary()
</script>