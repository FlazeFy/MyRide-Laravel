const colorPalleteChart = ['#46dca2', '#f55d86', '#FFC352', '#00b8ff']

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

            let chart = new ApexCharts(document.querySelector(`#${holder}`), options)
            chart.render()
        } else {
            $(`#${holder}`).html(`
                <h6 class="text-center">Data is Not Valid</h6>
            `)
        }
    } else {
        $(`#${holder}`).html(`
            <img src="{{asset('images/nodata.png')}}" class="img nodata-icon">
            <h6 class="text-center">No Data</h6>
        `)
    }
}

const generate_pie_chart = (title, holder, data) => {
    $(`#${holder}`).before(`<h2>${ucEachWord(title)}</h2>`)

    if(data.length > 0){
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
            $(`#${holder}`).html(`<h6 class="text-center">Data is Not Valid</h6>`)
        }
    } else {
        $(`#${holder}`).html(`
            <img src="{{asset('images/nodata.png')}}" class="img nodata-icon">
            <h6 class="text-center">No Data</h6>
        `)
    }
}

const generate_bar_chart = (title, holder, data) => {
    console.log(data)
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

            let chart = new ApexCharts(document.querySelector(`#${holder}`), options)
            chart.render()
        } else {
            $(`#${holder}`).html(`<h6 class="text-center">Data is Not Valid</h6>`)
        }
    } else {
        $(`#${holder}`).html(`
            <img src="{{asset('images/nodata.png')}}" class="img nodata-icon">
            <h6 class="text-center">No Data</h6>
        `)
    }
}