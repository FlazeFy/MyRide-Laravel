<div class="d-flex justify-content-between align-items-center">
    <div>
        <label style="font-size:var(--textXLG);" class="mb-0">Background Color</label>
        <p class="mb-0 text-secondary">Set your background color manually</p>
    </div>
    <button id="themeToggle" class="btn btn-success py-1"></button>
</div>

<script>
    $(document).ready(function () {
        const theme = localStorage.getItem("theme") || "light"
        const autoTheme = localStorage.getItem("autoTheme") || "off"

        const lightCaption = '<i class="fa-solid fa-sun me-1"></i> Light'
        const darkCaption  = '<i class="fa-solid fa-moon me-1"></i> Dark'

        $('#themeToggle').html(theme === "light" ? lightCaption : darkCaption)

        if (autoTheme === "on") {
            $("#themeToggle").prop("disabled", true)
        }

        $("#themeToggle").on("click", function () {
            if ($("body").hasClass("light")) {
                $("body").removeClass("light").addClass("dark")
                localStorage.setItem("theme", "dark")
                $(this).html(darkCaption)
            } else {
                $("body").removeClass("dark").addClass("light")
                localStorage.setItem("theme", "light")
                $(this).html(lightCaption)
            }
        })
    });
</script>
