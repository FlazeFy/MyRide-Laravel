<div class="container">
    <h2 class="mb-4">Convert Data CSV</h2>
    <form method="POST" action="/stats/convert/csv">
        @csrf
        <div class="d-flex justify-content-start">
            <div class="me-4">
                <label>Select Module</label>
                <select class="form-select" name="module" aria-label="Default select example">
                    <option value="Vehicle">My Garage - Vehicle</option>
                    <option value="Trip">Trip</option>
                    <option value="Clean">Clean</option>
                </select>
            </div>
            <div class="pt-4">
                <button class="btn btn-success rounded-pill py-2 px-3"><i class="fa-solid fa-cloud-arrow-down"></i> Download</button>
            </div>
        </div>
    </form>
</div>