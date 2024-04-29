<div class="text-center">
    @if(count($data) != 0)
        <h2 style="font-size:var(--textJumbo); font-weight:600;">{{ucwords(str_replace('_',' ',$ctx))}}</h2><br>
        <div id="Bar_{{$ctx}}"></div>
    @else
        <img src="{{asset('assets/nodata.png')}}" class="img nodata-icon">
        <h6 class="text-center">No Data</h6>
    @endif
</div>

<script type="text/javascript">
    var options = {
        series: [
        {
            name: 'Total',
            data: [
                <?php
                    foreach($data as $c){
                        echo $c->total.",";
                    }    
                ?>
            ],
        }, 
    ],
    chart: {
        height: '360',
        type: 'bar'
    },
    dataLabels: {
        enabled: false,
    },
    stroke: {
        curve: 'smooth'
    },
    xaxis: {
        type: 'category',
        categories: [
            <?php 
                foreach($data as $c){
                    echo "'".$c->context."',";
                }    
            ?>
        ],
        labels: {
            formatter: function (val) {
                return val.toFixed(0);
            }
        },
    },
   
    plotOptions: {
        bar: {
            borderRadius: 6,
            horizontal: true,
        },
    },
    tooltip: {
        y: {
            formatter: function (val) {
                val = val.toFixed(0)
                if(val == 0 || val == 1){
                    return val + " view";
                } else {
                    return val + " views";
                }
            }
        },
        marker: false,
        followCursor: true
    },
    stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        colors: ['#F9DB00','#009FF9'],
    };

    var chart = new ApexCharts(document.querySelector("#Bar_{{$ctx}}"), options);
    chart.render();
</script>