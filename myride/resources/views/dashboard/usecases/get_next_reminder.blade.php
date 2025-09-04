<h4>Next Reminder</h4>
<div id="next_reminder-holder"></div>

<script>
    const get_reminder = () => {
        Swal.showLoading()
        const ctx = 'next_reminder'

        const failedMsg = () => {
            Swal.fire({
                title: "Oops!",
                text: `Failed to get the reminder`,
                icon: "error"
            });
        }

        const generate_summary = (remind_at,reminder_title,reminder_context,reminder_body) => {
            const dateObj = new Date(remind_at.replace(" ", "T"))
            const date = dateObj.toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" })
            const time = dateObj.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit", hour12: true })
            let displayDate = date
            let chipClass = "bg-success"
            const now = new Date()

            const tomorrow = new Date(now)
            tomorrow.setDate(now.getDate() + 1)

            const isTomorrow = dateObj.getDate() === tomorrow.getDate() && dateObj.getMonth() === tomorrow.getMonth() && dateObj.getFullYear() === tomorrow.getFullYear()

            if (isTomorrow) {
                displayDate = "Tomorrow"
                chipClass = "bg-warning"
            }

            const diffHours = (dateObj - now) / (1000 * 60 * 60)
            if (diffHours > 0 && diffHours < 12) {
                chipClass = "bg-danger"
            }

            $(`#${ctx}-holder`).html(`
                <h4 class="fw-bold">${displayDate}</h4>
                <h2 class="fw-bold chip ${chipClass} d-inline-block" style="font-size:var(--textJumbo);">${time}</h2>
                <p class="text-secondary mb-0"><b>Notes:</b> ${reminder_body}</p>
            `)
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/reminder/next`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    localStorage.setItem(ctx,JSON.stringify(data))
                    localStorage.setItem(`last-hit-${ctx}`,Date.now())
                    generate_summary(data.remind_at,data.reminder_title,data.reminder_context,data.reminder_body)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    failedMsg()
                }
            });
        }

        if(ctx in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < summaryFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx))
                if(data){
                    generate_summary(data.remind_at,data.reminder_title,data.reminder_context,data.reminder_body)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg()
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }
    get_reminder()
</script>