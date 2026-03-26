<div>
    <img src="{{asset('assets/place.png')}}" alt='place.png' class="img img-fluid w-100 mb-3" style="max-width: 420px;">
    <h2>All Places That You've Visited</h2>
    <table class="table text-center table-bordered">
        <thead>
            <tr>
                <th scope="col" style="min-width: 180px">Place Name</th>
                <th scope="col" style="min-width: 140px">Origin</th>
                <th scope="col" style="min-width: 140px">Destination</th>
                <th scope="col">Partner</th>
            </tr>
        </thead>
        <tbody id="trip_place-holder">
            <tr>
                <td>Place A</td>
                <td>20</td>
                <td>10</td>
                <td>
                    <div class="d-flex flex-wrap gap-2">
                        <div class="chip m-0 text-nowrap mx-auto">Jhon Doe</div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>