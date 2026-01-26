<form action="/stats/toogle_month_year" method="POST" id="toogle_month_year-form">
    @csrf
    <select class="form-select" id="toogle_month_year-select" name="toogle_month_year"></select>
</form>

<script>
    const selectedMonthYear = `<?= session()->get('toogle_month_year') ?>`

    $(document).on('change','#toogle_month_year-select',function(){
        const keys = ['summary_fuel']
        resetLocalStorage(keys)
        $('#toogle_month_year-form').submit()
    })

    const getMonthYearOpt = () => {
        Swal.showLoading()
        const ctx = 'year_temp'
        const ctx_holder = 'toogle_month_year-select'

        const generate_month_year = (holder,data,selected) => {
            const now = getMonthYear()
            $(`#${holder}`).html(`
                <option value="all" ${selected === "all" ? "selected":""}>All</option>
                <option value="${now}" ${[now,""].includes(selected) ? "selected":""}>${now}</option>
            `)

            const years = data.map(dt => dt.year)
            years.forEach(dt => {
                for (let idx = 1; idx < 13; idx++) {
                    if(now !== `${idx}-${dt}`){
                        $(`#${holder}`).append(`<option value="${idx}-${dt}" ${selected === `${idx}-${dt}` ? "selected":""}>${idx}-${dt}</option>`)
                    }
                }
            })
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/user/my_year`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    localStorage.setItem(ctx,JSON.stringify(data))
                    localStorage.setItem(`last-hit-${ctx}`,Date.now())
                    generate_month_year(ctx_holder,data,selectedMonthYear)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    generateApiError(response, true)
                }
            })
        }

        if(ctx in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < statsFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx))
                if(data){
                    generate_month_year(ctx_holder,data,selectedMonthYear)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg(`get the driver list`)
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }

    getMonthYearOpt()
</script>