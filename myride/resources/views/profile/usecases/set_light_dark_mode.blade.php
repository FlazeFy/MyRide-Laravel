<h2>Setting</h2><hr>
<div class="d-flex justify-content-between">
    <label style="font-size:var(--textXLG);" class="mb-0">Background Color</label>
    <button id="themeToggle" class="btn btn-success theme-btn py-1"></button>
</div>

<script>
    $(document).ready(function () {
        const theme = localStorage.getItem("theme") || "light"
        const lightCaption = '<i class="fa-solid fa-sun me-1"></i> Light'
        const darkCaption = '<i class="fa-solid fa-moon me-1"></i> Dark'

        if(theme === "light"){
            $('#themeToggle').html(lightCaption)
        } else {
            $('#themeToggle').html(darkCaption)
        }

        $("#themeToggle").on("click", function () {
            if ($("body").hasClass("light")) {
                $("body").removeClass("light").addClass("dark")
                localStorage.setItem("theme", "dark")
                $('#themeToggle').html(darkCaption)
            } else {
                $("body").removeClass("dark").addClass("light")
                localStorage.setItem("theme", "light")
                $('#themeToggle').html(lightCaption)
            }
        });
    });
</script>
