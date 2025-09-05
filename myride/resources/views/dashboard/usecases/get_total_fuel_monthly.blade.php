<div id="stats_total_fuel_monthly_holder"></div>
<script>
    const context = '<?= session()->get('toogle_total_stats_fuel') ?? 'fuel_volume' ?>'
    
    const get_total_fuel_monthly = (year,context) => {
        Swal.showLoading()
        const title = 'Fuel Monthly'
        const ctx = `total_fuel_monthly_${context}_temp`
        const ctx_holder = 'stats_total_fuel_monthly_holder'

        const failedMsg = () => {
            Swal.fire({
                title: "Oops!",
                text: `Failed to get the stats ${title}`,
                icon: "error"
            });
        }
        const fetchData = () => {
            $.ajax({
                url: `/api/v1/stats/total/fuel/monthly/${context}/${year}`,
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
                        failedMsg()
                    } else {
                        template_alert_container(ctx_holder, 'no-data', "No fuel found for this context to generate the stats", 'add a fuel', '<i class="fa-solid fa-gas-pump"></i>','/fuel/add')
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
                    failedMsg()
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }
    get_total_fuel_monthly(2025,context)
</script>