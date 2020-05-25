(function ($) {
    $(document).ready(function () {

        $('.sidebar-toggle').pushMenu();

        Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });

        function precisionRound(number, precision) {
            var factor = Math.pow(10, precision);
            return Math.round(number * factor) / factor;
        }

        var options = {

            credits: {
                enabled: false
            },

            chart: {
                zoomType: 'x'
            },

            title: {},

            xAxis: {
                type                : 'datetime',
                dateTimeLabelFormats: { //force all formats to be hour:minute:second
                    second: '%H:%M:%S',
                    minute: '%H:%M:%S',
                    hour  : '%H:%M:%S',
                    day   : '%H:%M:%S',
                    week  : '%H:%M:%S',
                    month : '%H:%M:%S',
                    year  : '%H:%M:%S'
                }
            },

            yAxis: {
                title: {
                    text: null
                }
            },

            tooltip: {
                crosshairs : true,
                shared     : true,
                valueSuffix: 'EUR',
                formatter  : function (e) {
                    var buy    = precisionRound(this.points[1].point.low, 0);
                    var sell   = precisionRound(this.points[1].point.high, 0);
                    var spread = precisionRound(((sell - buy) * 100 / buy), 2);
                    var date   = Highcharts.dateFormat('%H:%M:%S', new Date(this.x));

                    if (spread > 0) {
                        spread = '<span class="positive" style="color: #54a71a">' + spread + '%</span>';
                    } else {
                        spread = '<span class="negative" style="color: #ff2f41;">' + spread + '%</span>';
                    }

                    var text = 'Spread at ' + date + ' is ' + spread + '<br/>';
                    text += '<span style="color:#00b7ff">Buy</span>: <b>' + Number(buy).toLocaleString() + '</b><br/>';
                    text += '<span style="color:#ff6e2d">Sell</span>: <b>' + Number(sell).toLocaleString() + '<br/>';
                    return text;
                }
            },
            legend : {},
            series : [{
                name  : 'Price',
                zIndex: 1,
                marker: {
                    fillColor: 'white',
                    lineWidth: 2,
                    lineColor: Highcharts.getOptions().colors[0]
                }
            }, {
                name       : 'Range',
                type       : 'arearange',
                zIndex     : 0,
                zoneAxis   : "x",
                linkedTo   : ':previous',
                lineWidth  : 0,
                fillOpacity: 0.3,
                marker     : {
                    enabled: false
                }
            }]
        };

        options.title.text     = 'Bitflyer / Kraken BTC';
        options.series[0].data = averages1;
        options.series[1].data = ranges1;
        options.series[1].zones = calculateGraphZones(ranges1);
        Highcharts.chart('graph1', options);

        options.title.text     = 'Bitflyer / Kraken ETH';
        options.series[0].data = averages2;
        options.series[1].data = ranges2;
        options.series[1].zones = calculateGraphZones(ranges2);
        Highcharts.chart('graph2', options);

        options.title.text      = 'Bitflyer / Kraken BCH';
        options.series[0].data  = averages3;
        options.series[1].data  = ranges3;
        options.series[1].zones = calculateGraphZones(ranges3);
        Highcharts.chart('graph3', options);

        // Highcharts.chart('graph3', options);
        // Highcharts.chart('graph4', options);
    });

    function calculateGraphZones(ranges) {
        var zones = [];
        $.each(ranges, function (key, value) {
            var newzoneSerie1 = {
                value: value[0]
            };
            if (value[1] > value[2]) {
                newzoneSerie1.color = '#ff2f41';
            } else {
                newzoneSerie1.color = '#54a71a';
            }
            zones.push(newzoneSerie1);
        });
        return zones;
    }

})(jQuery);