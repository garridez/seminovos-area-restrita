import { Map } from 'immutable';
import moment from 'moment';


export function createNewMessage(idConversa, idCadastroRemetente, mensagem) {

    const idChatMensagem = null;
    const enviadoEm = moment().format('YYYY-MM-DD HH:mm:ss.SSSS');
    const lidoEm = null;

    return Map({
        idChatMensagem,
        idConversa,
        idCadastroRemetente,
        mensagem,
        enviadoEm,
        lidoEm
    });
}