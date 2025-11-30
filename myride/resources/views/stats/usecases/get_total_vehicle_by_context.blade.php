<script>
    const get_total_vehicle_by_context = () => {
        Swal.showLoading()
        const ctx = 'total_vehicle_by_context_temp'
        const ctx_holder = 'stats_comparison-holder'
        const list_context = ['vehicle_fuel_status','vehicle_category','vehicle_status','vehicle_transmission']
        $(`#${ctx_holder}`).children().not(':first').remove()

        list_context.forEach(el => {
            $(`#${ctx_holder}`).append(`
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                    <div class="container-fluid d-flex flex-wrap justify-content-center"><div id="${el}-holder"></div></div>
                </div>
            `)
        });

        const fetchData = () => {
            const context = list_context.join(',')
            $.ajax({
                url: `/api/v1/stats/total/vehicle/${context}`,
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
                    data.forEach(dt => {
                        generate_pie_chart(dt.context.replaceAll('_',' '),`${dt.context}-holder`,dt.data)
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 404){
                        generate_api_error(response, true)
                    } else {
                        template_alert_container(ctx_holder, 'no-data', "No vehicle found for this context to generate the stats", 'add a vehicle', '<i class="fa-solid fa-warehouse"></i>','/vehicle/add')
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
                    data.forEach(dt => {
                        generate_pie_chart(dt.context.replaceAll('_',' '),`${dt.context}-holder`,dt.data)
                    });
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
    get_total_vehicle_by_context()
</script>