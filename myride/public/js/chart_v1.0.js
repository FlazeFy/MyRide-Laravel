const colorPalleteChart = ['#46dca2', '#f55d86', '#FFC352', '#00b8ff']

const warning_no_data_visualize = (holder) => {
    $(`#${holder}`).html(`
        <div class="container bg-warning">
            <h6><i class="fa-solid fa-triangle-exclamation"></i> Warning</h6>
            <p class="mb-0">No enough data to visualize</p>
        </div>
    `)
}

const generate_line_chart = (title, holder, data) => {
    $(`#${holder}`).before(`<h2>${ucEachWord(title)}</h2>`)

    if(data.length > 0){
        let keys = Object.keys(data[0])
        if(keys.length == 2 && (typeof data[0][keys[0]] === 'string' && Number.isInteger(data[0][keys[1]]) || typeof data[0][keys[1]] === 'string' && Number.isInteger(data[0][keys[0]]))){
            const totals = data.map(c => c[Number.isInteger(data[0][keys[1]]) ? keys[1] : keys[0]])
            const contexts = data.map(c => c[typeof data[0][keys[0]] === 'string' ? keys[0] : keys[1]])

            var options = {
                series: [{
                    name: title,
                    data: totals
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: false,        
                        tools: {
                            download: false 
                        }
                    },
                    zoom: {
                        enabled: false 
                    }
                },
                colors: colorPalleteChart,
                legend: {
                    position: 'bottom'
                },
                xaxis: {
                    categories: contexts,
                },
                stroke: {
                    curve: 'smooth'
                },
                responsive: [{
                    options: {
                        chart: {
                            width: 160
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            }

            $(`#${holder}`).wrap("<div style='overflow-x:auto;'><div style='min-width:560px;'></div></div>")
            let chart = new ApexCharts(document.querySelector(`#${holder}`), options)
            chart.render()
        } else {
            warning_no_data_visualize(holder)
        }
    } else {
        warning_no_data_visualize(holder)
    }
}

const generate_pie_chart = (title, holder, data) => {
    $(`#${holder}`).before(`<h2 class="text-center">${ucEachWord(title)}</h2><hr>`)

    if(data && data.length > 0){
        let keys = Object.keys(data[0])
        if(keys.length == 2 && (typeof data[0][keys[0]] === 'string' && Number.isInteger(data[0][keys[1]]) || typeof data[0][keys[1]] === 'string' && Number.isInteger(data[0][keys[0]]))){
            const totals = data.map(c => c[Number.isInteger(data[0][keys[1]]) ? keys[1] : keys[0]])
            const contexts = data.map(c => c[typeof data[0][keys[0]] === 'string' ? keys[0] : keys[1]])

            var options = {
                series: totals,
                chart: {
                    width: '360',
                    type: 'pie',
                },
                labels: contexts,
                colors: colorPalleteChart,
                legend: {
                    position: 'bottom'
                },
                responsive: [{
                    options: {
                        chart: {
                            width: 160
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            let chart = new ApexCharts(document.querySelector(`#${holder}`), options)
            chart.render()
        } else {
            warning_no_data_visualize(holder)
        }
    } else {
        warning_no_data_visualize(holder)
    }
}

const generate_bar_chart = (title, holder, data) => {
    $(`#${holder}`).before(`<h2 class="text-center">${ucEachWord(title)}</h2><hr>`)

    if(data.length > 0){
        let keys = Object.keys(data[0])
        if(keys.length == 2 && (typeof data[0][keys[0]] === 'string' && Number.isInteger(data[0][keys[1]]) || typeof data[0][keys[1]] === 'string' && Number.isInteger(data[0][keys[0]]))){
            const totals = data.map(c => c[Number.isInteger(data[0][keys[1]]) ? keys[1] : keys[0]])
            const contexts = data.map(c => c[typeof data[0][keys[0]] === 'string' ? keys[0] : keys[1]])

            var options = {
                series: [{
                    name: title,
                    data: totals
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true,        
                        tools: {
                            download: false 
                        }
                    }
                },
                colors: colorPalleteChart,
                legend: {
                    position: 'bottom'
                },
                plotOptions: {
                    bar: {
                        horizontal: false
                    }
                },
                xaxis: {
                    categories: contexts,
                },
                responsive: [{
                    options: {
                        chart: {
                            width: 160
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            $(`#${holder}`).wrap("<div style='overflow-x:auto;'><div style='min-width:560px;'></div></div>")
            let chart = new ApexCharts(document.querySelector(`#${holder}`), options)
            chart.render()
        } else {
            warning_no_data_visualize(holder)
        }
    } else {
        warning_no_data_visualize(holder)
    }
}

const generate_semi_gauge_chart = (title = null, holder, percentage) => {
    if(title){
        $(`#${holder}`).before(`<h2 class='title-chart'>${ucEachWord(title)}</h2>`)
    }

    if (0 >= percentage <= 100) {
        let fillColor

        if (percentage < 30) {
            fillColor = "var(--dangerColor)"
        } else if (percentage < 70) {
            fillColor = "var(--warningColor)"
        } else {
            fillColor = "var(--successColor)"
        }

        let options = {
            series: [percentage],
            chart: {
                height: 350,
                type: "radialBar",
                sparkline: {
                    enabled: true
                }
            },
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: {
                        margin: 0,
                        size: "70%",
                    },
                    dataLabels: {
                        name: { show: false},
                        value: {
                            show: true,
                            offsetY: -10, 
                            fontSize: "calc(1.5*var(--textXJumbo))",
                            fontWeight: 600,
                            formatter: (val) => `${val}%`
                        }
                    }
                }
            },
            fill: {
                colors: [fillColor],
            },
            labels: [percentage]
        }

        let chart = new ApexCharts(document.querySelector(`#${holder}`), options)
        chart.render()
    } else {
        warning_no_data_visualize(holder)
    }
}