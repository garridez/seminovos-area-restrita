import { Options } from 'highcharts/highstock';

export const chartDefaultHighStock = {
    chart: {
        type: 'column',
        zooming: {
            type: 'x',
        },
        alignTicks: false,
    },

    title: {
        text: 'Title!',
    },
    subtitle: {
        text: '',
    },
    rangeSelector: {
        buttons: [
            {
                type: 'day',
                count: 7,
                text: '7 dias',
                title: 'Ver ultimos 7 dias',
            },
            {
                type: 'month',
                count: 1,
                text: 'Mês',
                title: 'Ver ultimo mês',
            },
            {
                type: 'all',
                text: 'Tudo',
                title: 'Ver tudo',
            },
        ],
    },

    xAxis: { type: 'datetime' },
    yAxis: {
        title: {
            text: 'Quantidade',
        },
        allowDecimals: false,
        opposite: false,
    },

    tooltip: {
        crosshairs: true,
        shared: true,
    },
    plotOptions: {
        line: {
            marker: {
                enabled: false,
            },
        },
        series: {
            animation: false,
        },
    },
    series: [],
} as Options;
