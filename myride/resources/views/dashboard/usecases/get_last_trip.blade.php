<h2>Last Trip</h2><hr>
<div id="last_trip-holder"></div>

<script>
    const get_last_trip = () => {
        Swal.showLoading()
        const ctx = 'last_trip'

        const generate_last_trip = (created_at,trip_destination_name,trip_destination_coordinate,vehicle_plate_number,driver_username,vehicle_type) => {
            const dateObj = new Date(created_at.replace(" ", "T"))
            const date = dateObj.toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" })

            $(`#${ctx}-holder`).html(`
                <h4 class="fw-bold">${date}</h4>
                <span class="d-flex justify-align-center justify-content-center gap-2 mb-2">
                    <div class="plate-number m-0">${vehicle_plate_number}</div>
                    ${driver_username ? `<div class="plate-number m-0"><i class="fa-solid fa-user-tie"></i> ${driver_username}</div>` :''}
                </span>
                <p class="text-secondary mb-0"><b>Locate on:</b> ${trip_destination_coordinate ? `${trip_destination_coordinate} | `:''}${trip_destination_name}</p>
                ${trip_destination_coordinate ? `
                    <a class="btn btn-success py-1 mt-2 btn-set-route" data-trip-origin-coordinate="now" data-trip-destination-coordinate="${trip_destination_coordinate}" data-vehicle-type="${vehicle_type}"><i class="fa-solid fa-map-pin"></i> Set Route</a> 
                `:''}
            `)
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/trip/last`,
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
                    generate_last_trip(data.created_at,data.trip_destination_name,data.trip_destination_coordinate,data.vehicle_plate_number,data.driver_username)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()

                    if(response.status != 404){
                        generateApiError(response, true)
                    } else {
                        messageShortImage(`${ctx}-holder`,`{{asset('assets/empty.png')}}`,`there's no last the trip history`)
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
                    generate_last_trip(data.created_at,data.trip_destination_name,data.trip_destination_coordinate,data.vehicle_plate_number,data.driver_username)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg('get the last trip history')
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }
    get_last_trip()
</script>