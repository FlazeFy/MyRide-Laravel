<div class="col-xl-6 col-lg-6 col-md-12">
    <div class="container-fluid mb-0">
        <div id="stats_person_most_trip_with"></div>
    </div>
</div>

<script>
    const get_person_most_trip_with = () => {
        Swal.showLoading()
        const title = 'Person Most Trip With'
        const ctx = 'person_most_trip_with_temp'
        const ctx_holder = 'stats_person_most_trip_with'

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/stats/total/most_person_trip_with`,
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
                    generateBarChart(title,ctx_holder,data)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 404){
                        generateApiError(response, true)                    
                    } else {
                        templateAlertContainer(ctx_holder, 'no-data', "No trip found for this context to generate the stats", 'add a trip', '<i class="fa-solid fa-warehouse"></i>','/inventory/add')
                        $(`#${ctx_holder}`).prepend(`<h2 class='title-chart'>${ucEachWord(title)}</h2>`)
                    }
                }
            })
        }

        if(ctx in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < statsFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx))
                if(data){
                    generateBarChart(title,ctx_holder,data)
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
    get_person_most_trip_with()
</script>