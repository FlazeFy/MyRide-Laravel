<div id="stats_total_trip_monthly_holder"></div>
<script>
    const get_total_trip_monthly = (year) => {
        Swal.showLoading()
        const title = 'Trip Monthly'
        const ctx = 'total_trip_monthly_temp'
        const ctx_holder = 'stats_total_trip_monthly_holder'
        const type_chart =  '<?= session()->get('toogle_total_stats') ?>'

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/stats/total/trip/monthly/${year}`,
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
                    generate_line_chart(title,ctx_holder,data)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 404){
                        failedMsg(`get the stats ${title}`)
                    } else {
                        template_alert_container(ctx_holder, 'no-data', "No trip found for this context to generate the stats", 'add a trip', '<i class="fa-solid fa-warehouse"></i>','/trip/add')
                        $(`#${ctx_holder}`).prepend(`<h2 class='title-chart'>${ucEachWord(title)}</h2>`)
                    }
                }
            });
        }

        if(ctx in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < statsFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx))
                if(data){
                    generate_line_chart(title,ctx_holder,data)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg(`get the stats ${title}`)
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }
    get_total_trip_monthly(year)
</script>