<div id="stats_vehicle_monthly_trip_stats"></div>

<script>
    const get_vehicle_monthly_trip_stats = (year, vehicle_id) => {
        Swal.showLoading()
        const title = 'Total Trip Per Month'
        const ctx_holder = "stats_vehicle_monthly_trip_stats"

        $.ajax({
            url: `/api/v1/stats/total/trip/monthly/${year}/${vehicle_id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")    
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                generate_line_chart(title, ctx_holder, data)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status != 404){
                    Swal.fire({
                        title: "Oops!",
                        text: `Failed to get the stats ${title}`,
                        icon: "error"
                    });
                } else {
                    template_alert_container(ctx_holder, 'no-data', "No trip found for this context to generate the stats", 'add a trip', '<i class="fa-solid fa-car"></i>','/trip/add')
                    $(`#${ctx_holder}`).prepend(`<h2 class='title-chart'>${ucEachWord(title)}</h2>`)
                }
            }
        });
    }
    get_vehicle_monthly_trip_stats(<?= date('Y') ?>,"<?= $id ?>")
</script>