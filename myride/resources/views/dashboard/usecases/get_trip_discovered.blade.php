<h4>Trip Discovered</h4>
<div id="trip_discovered-holder"></div>

<script>
    const get_trip_discovered = () => {
        Swal.showLoading()
        const ctx = 'trip_discovered'

        const generate_trip_discovered = (total_trip, distance_km, last_updated, ctx) => {
            const dateObj = new Date(last_updated.replace(" ", "T"))
            const date = dateObj.toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" })
            const time = dateObj.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit", hour12: true })

            let chipClass = "bg-danger"
            if(parseInt(distance_km) < 25000){
                chipClass = "bg-success"
            } else if(parseInt(distance_km) < 75000){
                chipClass = "bg-warning"
            }

            $(`#${ctx}-holder`).html(`
                <h4 class="fw-bold">${total_trip} Trip</h4>
                <h2 class="fw-bold chip ${chipClass} d-inline-block" style="font-size:var(--textJumbo);">${distance_km} km</h2>
                <p class="text-secondary mb-0"><b>Last Updated:</b> ${date}</p>
            `)
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/trip/discovered`,
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
                    generate_trip_discovered(data.total_trip,data.distance_km,data.last_update,ctx)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status !== 404){
                        failedMsg(`get the trip`)
                    } else {
                        message_short_image(`${ctx}-holder`,`{{asset('assets/empty.png')}}`,`there's no trip history`)
                    }
                }
            });
        }

        if(ctx in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < summaryFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx))
                if(data){
                    generate_trip_discovered(data.total_trip,data.distance_km,data.last_update,ctx)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg(`get the trip`)
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }
    get_trip_discovered()
</script>