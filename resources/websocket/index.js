
import HttpServer from './http.server';
import WebsocketServer from './websocket.server';
import ApiClient from './SnBH/Api/ApiClient';

const httpServer = HttpServer();

const apiClient = (function () {
    let serverUrl = process.env.SNBH_API_HOST || 'http://localhost:8081';
    serverUrl = 'http://dev.seminovos.com.br:8081/';
    // serverUrl = 'http://api2.seminovosbh.com.br';

    let headers = {};
    let options = {};
    return new ApiClient(serverUrl, headers, options);
})();


WebsocketServer(httpServer, apiClient);