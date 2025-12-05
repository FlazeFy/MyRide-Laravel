<div class="container-fluid d-block d-md-flex justify-content-between align-items-center gap-2">
    <h2>Convert Data CSV</h2>
    <form method="POST" action="/stats/convert/csv">
        @csrf
        <div class="d-flex justify-content-start gap-3">
            <div>
                <label class="mb-0">Select Module</label>
                <select class="form-select py-1 mb-0" name="module" aria-label="Default select example">
                    <option value="Vehicle">Garage / Vehicle</option>
                    <option value="Trip">Trip</option>
                    <option value="Wash">Wash</option>
                </select>
            </div>
            <div>
                <button class="btn btn-success rounded-pill py-2 px-3 h-100">
                    <i class="fa-solid fa-cloud-arrow-down"></i><span class="d-md-none d-lg-inline"> Download</span>
                </button>
            </div>
        </div>
    </form>
</div>