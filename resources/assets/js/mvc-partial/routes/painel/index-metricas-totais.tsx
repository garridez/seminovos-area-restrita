import highstock, { SeriesOptionsType, Options } from 'highcharts/highstock';
import accessibility from 'highcharts/modules/accessibility';
import exporting from 'highcharts/modules/exporting';
import exportingData from 'highcharts/modules/export-data';
import HighchartsReact from 'highcharts-react-official';

import { JSX } from 'react';
import { createRoot } from 'react-dom/client';

declare global {
    interface Window {
        metricasTotais: SeriesOptionsType[];
    }
}

export const seletor = '.c-painel.a-index';
export default () => {
    $('body').on('click', '.nav-tabs .nav-link', function (e) {
        e.preventDefault();
        const $this = $(this);
        $this.closest('.nav-tabs').find('.nav-link').removeClass('active');
        $this.addClass('active');
    });

    accessibility(highstock);
    exporting(highstock);
    exportingData(highstock);

    const metricasTotais: SeriesOptionsType[] = window.metricasTotais;
    var i = 0;

    var navLis: JSX.Element[] = [];
    var charts: JSX.Element[] = [];
    for (const label in metricasTotais) {
        i++;
        const id = 'metrica-' + label + '-' + Math.trunc(Math.random() * 10000);
        const metrica = metricasTotais[label];
        navLis.push(
            <li key={id}>
                <a
                    href={'#' + id}
                    className={'nav-link' + (i === 1 ? ' active' : '')}
                    data-toggle="collapse"
                >
                    <h4>{metricasTotais[label].name}</h4>
                </a>
            </li>,
        );

        charts.push(
            <div
                id={id}
                key={id}
                className={'collapse' + (i === 1 ? ' show' : '') + ' w-100'}
                data-parent={'#charts-root-container'}
            >
                <div>{getChart(metrica)}</div>
            </div>,
        );
    }

    const metricasHtml = (
        <div id="charts-root-container">
            <ul className="nav nav-tabs nav-fill">{navLis}</ul>
            <div className="charts">{charts}</div>
        </div>
    );
    const metricasContainer = document.querySelector('.metricas-container');
    if (metricasContainer) {
        createRoot(metricasContainer).render(metricasHtml);
    }
};

function getChart(serie: SeriesOptionsType) {
    var custom = {
        ...serie.custom,
    };

    return (
        <HighchartsReact
            highcharts={highstock}
            options={
                {
                    chart: {
                        type: 'column',
                        zooming: {
                            type: 'x',
                        },
                        alignTicks: false,
                    },

                    title: {
                        text: custom?.description,
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
                    yAxis: [
                        {
                            title: {
                                text: serie.name,
                            },
                            allowDecimals: false,
                            opposite: false,
                        },
                    ],
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
                        column: {
                            stacking: 'normal',
                        },
                    },
                    series: [serie],
                } as Options
            }
        />
    ); /**/
}
