
class ApiResponse {
    /**
     * Status do retorno da API e não do HTTP
     */
    status;
    httpResponse;
    totalTime;
    bodyJson;
    _isFromCache;

    constructor(httpResponse, totalTime, isFromCache = false) {
        this.httpResponse = httpResponse;
        this._isFromCache = isFromCache;
        this.totalTime = totalTime;
        this.status = (this.json() || {}).status;
        this.data = this.getData();
    }

    getHttpResponse() {
        return this.httpResponse;
    }

    /**
     * Retorna a saída da requisição como json
     * 
     * @returns {Object}
     */
    json() {
        return this.httpResponse.data;
    }

    /**
     * Retorna a chave 'data' dentro do retorno
     * @returns {object}
     */
    getData() {
        var json = this.json() || {};
        return json.data || json;
    }

    getTotalTime() {
        return this.totalTime;
    }

    isFromCache() {
        return this._isFromCache;
    }
}



export default ApiResponse;