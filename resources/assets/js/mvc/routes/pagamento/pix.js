import AdvancedAlerts from '../../../components/AdvancedAlerts';

export const seletor = '.c-pagamento.a-pagamento-pix';
export const callback = async ($) => {
    // Get url params
    const urlParams = new URLSearchParams(window.location.search);
    const idVeiculo = urlParams.get('idVeiculo');
    console.log({ idVeiculo });
    const ulPagamentoStatus = `/carro/${idVeiculo}/checkout/pagamento-status`;

    var pagDataCadastro = false;

    async function getStatus() {
        var response = await fetch(ulPagamentoStatus);
        var data = await response.json();
        console.log(data);
        return data;
    }

    async function refreshStatus() {
        var status = await getStatus();

        switch (status.ultimoPagamento.status) {
            case '1':
                pagDataCadastro = new Date(status.ultimoPagamento.data_cadastro);
                pagDataCadastro.setMinutes(pagDataCadastro.getMinutes() + 5);
                console.log(pagDataCadastro);
                break;
            case '2': {
                console.log('Pagamento confirmado');
                const tipo = window.location.pathname.split('/').filter(Boolean)[0];
                window.location.href = `/${tipo}/${idVeiculo}/checkout/aprovado`;
                break;
            }
            case '3':
                AdvancedAlerts.warning({
                    title: 'Pagamento cancelado',
                    text: 'O pagamento para esse veículo foi cancelado.<br>\
                    Provalvemente o tempo de pagamento expirou ou houve algum problema com o pagamento.<br>\
                    Por favor, tente novamente',
                    time: false,
                    closeCallback: () => {
                        const tipo = window.location.pathname.split('/').filter(Boolean)[0];
                        window.location.href = `/${tipo}/${idVeiculo}?editar=planos#plano&trocarPlano`;
                    },
                });
                return;
        }
        setTimeout(() => {
            refreshStatus();
        }, 1000);
    }

    refreshStatus();

    //console.log('ok');

    setInterval(() => {
        if (!pagDataCadastro) {
            return;
        }
        var now = new Date();
        var diff = pagDataCadastro - now;
        if (diff < 0) {
            $('.pix-timer .timer').text('00:00');
            return;
        }

        var seconds = Math.floor(diff / 1000);
        var minutes = Math.floor(seconds / 60);

        //pagDataCadastro.setSeconds(pagDataCadastro.getSeconds() - 1);
        var minutesStr = minutes.toString().padStart(2, '0');
        var secondsStr = (seconds % 60).toString().padStart(2, '0');

        $('.pix-timer .timer').text(`${minutesStr}:${secondsStr}`);
    }, 1000);
};
