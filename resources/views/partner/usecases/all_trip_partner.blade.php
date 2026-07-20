<style>
    #partner-holder .container {
        transform: scale(0.9);
        margin-bottom: 0;
        transition: transform 0.3s ease;
    }
    #partner-holder .container:nth-child(2), #partner-holder .container:nth-child(3) {
        transform: scale(0.95);
        margin-bottom: var(--spaceLG);
    }
    #partner-holder .container:nth-child(1) {
        transform: scale(1);
        box-shadow: 0 0 20px rgba(255, 193, 7, 0.8), 0 0 30px rgba(255, 193, 7, 0.7), 0 0 40px rgba(255, 193, 7, 0.6);
        margin-bottom: var(--spaceJumbo) !important;
        margin-top: var(--spaceLG) !important;
    }
    #partner-holder .container:nth-child(2) {
        box-shadow: 0 0 17.5px rgba(192, 192, 192, 0.75), 0 0 27.5px rgba(192, 192, 192, 0.65), 0 0 37.5px rgba(192, 192, 192, 0.55);
    }
    #partner-holder .container:nth-child(3) {
        box-shadow: 0 0 15px rgba(205, 127, 50, 0.7), 0 0 25px rgba(205, 127, 50, 0.6), 0 0 35px rgba(205, 127, 50, 0.5);
    }
</style>

<div>
    <img src="{{asset('assets/partner.png')}}" alt='partner.png' class="img img-fluid w-100 mb-3" style="max-width: 420px;">
    <h2>Your Adventure Partners</h2>
    <div id="partner-holder"></div>
</div>

<script>
    const getAllPartner = (page) => {
        const holder = 'partner-holder'

        $.ajax({
            url: `/api/v1/stats/partner`,
            type: 'GET',
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                $(`#${holder}`).empty()
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                
                data.forEach((dt, idx) => {
                    const rank = idx + 1
                    $(`#${holder}`).append(`
                        <div class="container text-start">
                            <h4>#${rank}${rank === 1 ? 'st' : rank === 2 ? 'nd' : rank === 3 ? 'rd' : 'th' } ${dt.name}</h4>
                            <div class="d-flex mb-2">
                                <div class="chip bg-primary mb-0 ms-0">Total Trip : ${dt.total_trip}</div>
                                <div class="chip bg-danger mb-0 ms-0">Favorite Day : ${dt.favorite_day}</div>
                                <div class="chip bg-success mb-0 ms-0">Total Distance : ${dt.total_distance} Km</div>
                            </div>
                            <p class="mb-0 fst-italic">Last trip at ${dt.last_trip}</p>
                            ${rank < 4 ? `<img src="{{asset('assets/${rank}-medal.png')}}" alt='${rank}-medal.png' class="img img-fluid w-100 mb-3" style="max-width: 100px; position: absolute; top: -5px; right: 20px;">` : ''}
                        </div>
                    `)
                })
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                response.status !== 404 ? generateApiError(response, true) : templateAlertContainer(holder, 'no-data', "No partner found", null, '<i class="fa-solid fa-rotate-left"></i>',null)
            }
        })
    }
    getAllPartner()
</script>