
export function isOnline(ultimaVezVisto) {
    if (!(ultimaVezVisto instanceof Date)) {
        ultimaVezVisto = ultimaVezVisto.ultimaVezVisto;
    }

    if (typeof ultimaVezVisto === 'string') {
        ultimaVezVisto = new Date(ultimaVezVisto);
    }
    if (ultimaVezVisto === null) {
        return false;
    }
    var diffInMinutes = (new Date - ultimaVezVisto) / 1000 / 60;
    return diffInMinutes <= 10;
}

export function filterUser(idCadastro, data) {
    var newData = {};

    if (idCadastro === data.idCadastro) {
        newData.idCadastro = data.idCadastroInteressado;
        newData.responsavelNome = data.responsavelNomeInteressado;
    } else{
        newData.idCadastro = data.idCadastro;
        newData.responsavelNome = data.responsavelNome;        
    }
    
    return newData;
}

export default {
    isOnline,
    filterUser
}