<form action="/dashboard/toogle_year" method="POST" id="toogle_year_select">
    @csrf
    <label>Select Year</label>
    <select class="form-select" id="toogle_year" name="toogle_year"></select>
</form>

<script>
    $(document).on('change','#toogle_year',function(){
        const keys = ['total_fuel_monthly_fuel_price_total_temp','total_fuel_monthly_fuel_volume_temp','total_trip_monthly_temp']
        keys.forEach(dt => {
            localStorage.removeItem(dt)
            localStorage.removeItem(`last-hit-${dt}`) 
        });
        $('#toogle_year_select').submit()
    })
    
    const get_available_year = () => {
        Swal.showLoading()
        $.ajax({
            url: `/api/v1/user/my_year`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                const selected_year = year;

                data.forEach(el => {
                    $('#toogle_year').append(`<option value="${el.year}" ${selected_year == el.year ? 'selected' :''}>${el.year}</option>`) 
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                generateApiError(response, true)
            }
        });
    }
    get_available_year()
</script>