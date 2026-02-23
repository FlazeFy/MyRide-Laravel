<script>
    const generateMonthlySummary = (monthYear, totalTrip, totalService, totalWash, totalFuel, mostFavoritePerson, mostTripCategory, targetElement) => {
        let elFavoritePerson = ''
        let elMostCategory = ''

        if (mostFavoritePerson && mostFavoritePerson.length > 0) {
            mostFavoritePerson.forEach(el => {
                elFavoritePerson += `
                    <tr>
                        <td class="text-dark">${el.context}</td>
                        <td class="text-dark">${el.total}</td>
                    </tr>
                `
            })
        }

        if (mostTripCategory && mostTripCategory.length > 0) {
            mostTripCategory.forEach(el => {
                elMostCategory += `
                    <tr>
                        <td class="text-dark">${el.context}</td>
                        <td class="text-dark">${el.total}</td>
                    </tr>
                `
            })
        }
        
        $(targetElement).append(`
            <div class="container mt-5 bg-warning text-dark">
                <h3>${monthYear}'s Summary</h3>
                <hr class="bg-dark">
                <div class="row tex">
                    <div class="col-lg-3 col-md-4 col-sm-6 mx-auto">
                        <h4 class="fw-bold">${totalTrip.total} Trip</h4>
                        <h6 class="text-dark">${totalTrip.distance} Km</h6>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mx-auto">
                        <h4 class="fw-bold">${totalService.total} Service</h4>
                        <h6 class="text-dark">Rp. ${(totalService.amount / 1000).toLocaleString()}K</h6>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mx-auto">
                        <h4 class="fw-bold">${totalWash.total} Wash</h4>
                        <h6 class="text-dark">Rp. ${(totalWash.amount / 1000).toLocaleString()}K</h6>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mx-auto">
                        <h4 class="fw-bold">${totalFuel.total} Trip</h4>
                        <h6 class="text-dark">Rp. ${(totalFuel.amount / 1000).toLocaleString()}K</h6>
                    </div>
                    <div class="col-lg-6 col-sm-12 pt-3">
                        <h4 class="fw-bold">Favorite Person</h4>
                        <table class="table">
                            <thead>
                                <tr style="font-weight: 600">
                                    <td>Person Name</td>
                                    <td>Total</td>
                                </tr>
                            </thead>
                            <tbody>
                                ${elFavoritePerson}
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6 col-sm-12 pt-3">
                        <h4 class="fw-bold">Most Category</h4>
                        <table class="table">
                            <thead>
                                <tr style="font-weight: 600">
                                    <td>Category</td>
                                    <td>Total</td>
                                </tr>
                            </thead>
                            <tbody>
                                ${elMostCategory}
                            </tbody>
                        </table>
                    </div>
                </div>
               
            </div>
        `)
    }
</script>