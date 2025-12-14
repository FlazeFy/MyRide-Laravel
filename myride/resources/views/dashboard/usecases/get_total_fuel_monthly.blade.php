<div id="stats_total_fuel_monthly_holder"></div>
<script>
    const context = '<?= session()->get('toogle_total_stats_fuel') ?? 'fuel_volume' ?>'
    
    const get_total_fuel_monthly = (year,context) => {
        Swal.showLoading()
        const title = `Fuel Consumption Monthly`
        const ctx = `total_fuel_monthly_${context}_temp`
        const ctx_holder = 'stats_total_fuel_monthly_holder'

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/stats/total/fuel/monthly/${context}/${year}`,
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
                    generateLineChart(title,ctx_holder,data)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 404){
                        generateApiError(response, true)
                    } else {
                        templateAlertContainer(ctx_holder, 'no-data', "No fuel found for this context to generate the stats", 'add a fuel', '<i class="fa-solid fa-gas-pump"></i>','/fuel/add')
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
                    generateLineChart(title,ctx_holder,data)
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
    get_total_fuel_monthly(year,context)
</script>