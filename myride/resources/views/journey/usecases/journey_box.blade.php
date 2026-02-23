<script>
    const generateJourneyBox = (category, context, date, targetElement) => {
        $(targetElement).append(`
            <div class="container mt-3 bg-warning text-start">
                <div class="d-flex align-items-center mb-3">
                    <h5 class="chip bg-success mb-0 ms-0">${category}</h5>
                    <p class="text-dark mb-0">${context}</p>
                </div>
                <p class="mb-0 fst-italic text-dark" style="font-size: var(--textMD)">At ${date}</p>
            </div>
        `)
    }
</script>