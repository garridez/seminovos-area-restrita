
export default {
    idStatusAtivo: [
        2 /* Ativo */,
        9 /* Fotos Alteradas */
    ],
    isAtivo: function (idStatus) {
        return this.idStatusAtivo.indexOf(parseInt(idStatus, 10)) !== -1;
    }
}