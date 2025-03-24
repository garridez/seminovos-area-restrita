import highcharts, { Options,SeriesOptionsType } from 'highcharts/highstock';
import accessibility from 'highcharts/modules/accessibility';
import exportingData from 'highcharts/modules/export-data';
import exporting from 'highcharts/modules/exporting';
import HighchartsReact from 'highcharts-react-official';
import $ from 'jquery';
import merge from 'lodash/merge';
import { JSX } from 'react';
import { createRoot } from 'react-dom/client';

import highChartsLangPTBR from '../../../components/highChartsLangPTBR';
import { chartDefaultHighStock } from './helpers/chartDefaults';
import * as chartOptionsMetricasTotais from './helpers/chartOptionsMetricasTotais';

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
    dateRange();
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

    const navLis: JSX.Element[] = [];
    const charts: JSX.Element[] = [];

    let label: keyof MetricasSerie;
    for (label in metricasSerie) {
        const id = 'metrica-' + label + '-' + Math.trunc(Math.random() * 10000);
        const metrica = metricasSerie[label];
        if (!metrica) {
            continue;
        }
        const show = false;
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

function dateRange() {
    const $ctx = $('form.filtro-date');
    const $dateStart = $ctx.find('[name="date-start"]');
    const $dateEnd = $ctx.find('[name="date-end"]');

    $dateStart.on('change', function () {
        const val = $(this).val();
        console.log({ val });
        $dateEnd.attr('min', String(val));
    });
    $dateEnd.on('change', function () {
        const val = $(this).val();
        console.log({ val });
        $dateStart.attr('max', String(val));
    });
}
