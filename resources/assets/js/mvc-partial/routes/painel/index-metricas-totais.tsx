import highcharts, { SeriesOptionsType, Options } from 'highcharts';
import accessibility from 'highcharts/modules/accessibility';
import exporting from 'highcharts/modules/exporting';
import exportingData from 'highcharts/modules/export-data';
import HighchartsReact from 'highcharts-react-official';
import highChartsLangPTBR from '../../../components/highChartsLangPTBR';

import { JSX } from 'react';
import { createRoot } from 'react-dom/client';

import { chartDefaultHighStock } from './helpers/chartDefaults';
import * as chartOptionsMetricasTotais from './helpers/chartOptionsMetricasTotais';

import merge from 'lodash/merge';

type MetricasSerie = {
    acesso?: SeriesOptionsType;
    impressao?: SeriesOptionsType;
    telefone?: SeriesOptionsType;
};

declare global {
    interface Window {
        metricasSerie: MetricasSerie;
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

    highChartsLangPTBR(highcharts);
    accessibility(highcharts);
    exporting(highcharts);
    exportingData(highcharts);

    const metricasSerie = window.metricasSerie;
    var i = 0;

    var navLis: JSX.Element[] = [];
    var charts: JSX.Element[] = [];

    let label: keyof MetricasSerie;
    for (label in metricasSerie) {
        i++;
        const id = 'metrica-' + label + '-' + Math.trunc(Math.random() * 10000);
        const metrica = metricasSerie[label];
        if (!metrica) {
            continue;
        }
        const show = i === 1;
        navLis.push(
            <li key={id}>
                <a
                    href={'#' + id}
                    className={'nav-link' + (show ? ' active' : '')}
                    data-toggle="collapse"
                >
                    <h5 className="my-0">{metrica.name}</h5>
                </a>
            </li>,
        );
        const chartOptions = chartOptionsMetricasTotais[label] || {};
        console.log(chartOptions);
        const metricaMerged = merge({}, metrica, {
            dataLabels: {
                enabled: true,
            },
        } as SeriesOptionsType) as SeriesOptionsType;

        charts.push(
            <div
                id={id}
                key={id}
                className={'collapse' + (show ? ' show' : '') + ' w-100'}
                data-parent={'#charts-root-container'}
            >
                <div>{getChart(chartOptions, metricaMerged)}</div>
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

function getChart(chartOptions: Options, serie: SeriesOptionsType) {
    const options = merge({}, chartDefaultHighStock, chartOptions, {
        series: [serie],
    } as Options) as Options;
    console.log(options);

    return (
        <HighchartsReact highcharts={highcharts} constructorType={'stockChart'} options={options} />
    );
}
