<div class="d-block d-md-flex justify-content-between align-items-center mb-2">
    <div>
        <label style="font-size:var(--textXLG);" class="mb-0">Auto Background</label>
        <p class="mb-0 text-secondary">Ligth / Dark Mode theme based on your current local time</p>
    </div>
    <div class="d-grid d-md-inline-block">
        <button id="autoThemeToggle" class="btn py-1 mt-2 mt-md-0 w-100 w-md-auto"></button>
    </div>
</div>

<script>
    $(document).ready(function () {
        const autoTheme = localStorage.getItem("autoTheme") || "off"
        const onCaption = '<i class="fa-solid fa-toggle-on me-1"></i> On'
        const offCaption = '<i class="fa-solid fa-toggle-off me-1"></i> Off'

        if (autoTheme === "on") {
            $('#autoThemeToggle').html(onCaption).addClass('btn-success').removeClass('btn-danger')
            applyAutoTheme()
            $("#themeToggle").prop("disabled", true)
        } else {
            $('#autoThemeToggle').html(offCaption).addClass('btn-danger').removeClass('btn-success')
            $("#themeToggle").prop("disabled", false)
        }

        $("#autoThemeToggle").on("click", function () {
            if (autoTheme === "on") {
                localStorage.setItem("autoTheme", "off")
                $(this).html(offCaption).removeClass('btn-success').addClass('btn-danger')
                $("#themeToggle").prop("disabled", false)
            } else {
                localStorage.setItem("autoTheme", "on")
                $(this).html(onCaption).removeClass('btn-danger').addClass('btn-success')
                applyAutoTheme()
                $("#themeToggle").prop("disabled", true)
            }

            location.reload()
        })
    })
</script>
