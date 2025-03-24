import Alert from './Alerts';
function showError(body: string, title: string, time: number) {
    Alert.error(body, title, time);
}
export type ApiResponseType = {
    status?: number;
    title?: string;
    detail?: string;
    messages?: string | string[];
};
/**
 * A função verifica a resposta da API está ok
 *  Se não estiver OK, a função retona FALSE e mostra um alerta com a msg da API
 *  Se estiver OK, a função retorna TRUE e não faz mais nada
 * @param obj apiResponse resposta da API
 * @param int time Tempo de exibição do alert em milissegundos
 * @returns {Boolean}
 */
export default function (apiResponse: ApiResponseType, time: number = 15_000) {
    time = time || 15000;
    if (
        typeof apiResponse === 'object' &&
        apiResponse.status &&
        apiResponse.status >= 200 &&
        apiResponse.status <= 299
    ) {
        return true;
    }
    if (!apiResponse) {
        apiResponse = {};
    }
    let title = 'Houve um problema...';
    let body = '';
    if (apiResponse.title && apiResponse.title !== 'Method Not Allowed') {
        title = apiResponse.title;
    }
    if (apiResponse.detail) {
        body += apiResponse.detail;
    } else {
        body += 'Houve um problema ao processar sua solicitação.<br>Tente novamente.';
    }
    const messages = apiResponse.messages;
    if (messages) {
        body += '<br>';
        if (Array.isArray(messages)) {
            body += messages.join('<br>');
            /**
             * Quanto mais mensagens tiver, por mais tempo a mensagem será exibida
             */
            time += messages.length * 1000;
        } else {
            body += messages;
        }
    }
    showError(body, title, time);
    return false;
}
