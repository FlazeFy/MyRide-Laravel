<style>
    #nav_scroll-holder {
        position: fixed; 
        right: var(--spaceLG); 
        bottom: var(--spaceLG); 
        z-index: 1000; 
    }
    #scroll-to-top-btn {
        color: var(--darkColor) !important;
    }
</style>

<div id="nav_scroll-holder"></div>
<script>
    const handle_scroll_top_btn = () => {
        if (window.scrollY > window.innerHeight) {
            if ($('#scroll-to-top-btn').length === 0) {
                $('#nav_scroll-holder').prepend(`
                    <button class="btn btn-warning w-100 px-3" id="scroll-to-top-btn">
                        <i class="fa-solid fa-arrow-up"></i><span class="d-none d-md-inline"> Scroll to Top</span>
                    </button>
                `);

                $('#scroll-to-top-btn').on('click', function () {
                    $('html, body').animate({ scrollTop: 0 }, 200)
                });
            }
        } else {
            $('#scroll-to-top-btn').remove()
        }
    };
    handle_scroll_top_btn()
    $(window).on('scroll resize', () => {
        handle_scroll_top_btn()
    });
</script>