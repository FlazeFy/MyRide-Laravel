<div style="margin: 10vh 0; max-width: 1080px;" class="d-block mx-auto">
    <div class="bg-primary text-white rounded-pill px-3 py-2 d-block mx-auto" style="width:140px;">Summary</div>
    <br>
    <h1 class="fw-bold" style="font-size:50px;">Facts About Us</h1>
    <div class="row mt-4">
        <div class="col-lg-4 col-md-6 col-12 mx-auto">
            <h2 id="total_user">-</h2>
            <p>Total User</p>
        </div>
        <div class="col-lg-4 col-md-6 col-12 mx-auto">
            <h2 id="total_vehicle">-</h2>
            <p>Total Vehicle</p>
        </div>
        <div class="col-lg-4 col-md-6 col-12 mx-auto">
            <h2 id="total_trip">-</h2>
            <p>Total Trip</p>
        </div>
        <div class="col-lg-4 col-md-6 col-12 mx-auto">
            <h2 id="total_clean">-</h2>
            <p>Total Clean History</p>
        </div>
        <div class="col-lg-4 col-md-6 col-12 mx-auto">
            <h2 id="total_service">-</h2>
            <p>Total Service History</p>
        </div>
        <div class="col-lg-4 col-md-6 col-12 mx-auto">
            <h2 id="total_driver">-</h2>
            <p>Total Driver</p>
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
                Swal.close()
                Swal.fire({
                    title: "Oops!",
                    text: `Something went wrong`,
                    icon: "error"
                });
            }
        });
    }
    get_summary()
</script>