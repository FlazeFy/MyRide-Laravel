<div style="margin: 10vh 0; max-width: 1080px;" class="d-block mx-auto">
    <br>
    <h2 class="fw-bold" style="font-size:50px;">Facts About Us</h2>
    <div class="row mt-4">
        <div class="col-lg-4 col-md-4 col-6 mx-auto">
            <div class="container-landing bg-danger">
                <h2 id="total_user">-</h2>
                <h5>User</h5>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-6 mx-auto">
            <div class="container-landing bg-danger">
                <h2 id="total_vehicle">-</h2>
                <h5>Vehicle</h5>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-6 mx-auto">
            <div class="container-landing bg-danger">
                <h2 id="total_trip">-</h2>
                <h5>Trip</h5>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-6 mx-auto">
            <div class="container-landing bg-danger">
                <h2 id="total_clean">-</h2>
                <h5>Clean History</h5>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-6 mx-auto">
            <div class="container-landing bg-danger">
                <h2 id="total_service">-</h2>
                <h5>Service History</h5>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-6 mx-auto">
            <div class="container-landing bg-danger">
                <h2 id="total_driver">-</h2>
                <h5>Driver</h5>
            </div>
        </div>
    </div>
</div>

<script>
    const get_summary = () => {
        $.ajax({
            url: `/api/v1/stats/summary`,
            type: 'GET',
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json")  
            },
            success: function(response) {
                Swal.close()
                const data = response.data
                $('#total_user').text(data.total_user)
                $('#total_vehicle').text(data.total_vehicle)
                $('#total_clean').text(data.total_clean)
                $('#total_service').text(data.total_service)
                $('#total_driver').text(data.total_driver)
                $('#total_trip').text(data.total_trip)
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                generate_api_error(response, true)
            }
        });
    }
    get_summary()
</script>