<style>
    .btn-trip-box {
        color: var(--whiteColor) !important;
        padding: var(--spaceLG) !important;
        margin-bottom: var(--spaceLG);
        width: 100%;
        text-align: left !important;
        border: 1.5px solid var(--whiteColor) !important;
        border-radius: var(--roundedMD) !important;
    }
    .btn-trip-box:hover {
        transform: scale(1.025);
    }
</style>

<div id="trip-content-holder"></div>

<script>
    const build_layout_trip = (dt) => {
        if(dt){
            dt.data.forEach(el => {
                template_trip_box(el,'#trip-content-holder')
            });
        }
    }
</script>