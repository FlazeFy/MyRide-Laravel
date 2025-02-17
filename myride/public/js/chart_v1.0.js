const generate_line_chart = (title, holder, data) => {
    $(`#${holder}`).before(`<h2 class='title-chart'>${ucEachWord(title)}</h2>`)

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
                    type: 'line',
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
                colors: ['#F9DB00', '#009FF9', '#F78A00', '#42C9E7'],
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
