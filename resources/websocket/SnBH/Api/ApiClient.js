import axios from 'axios';
import ApiResponse from './ApiResponse';

class ApiClient {

    serverUrl;
    headers;
    options;

    constructor(serverUrl, headers, options) {
        this.serverUrl = serverUrl.replace(/\/+$/, '');
    }

    apiCall(method, path, body, useCache = false) {
        method = (method || 'Get').toUpperCase();
        body = body || {};

        var requestOptions = {
            url: path,
            method: method,
            baseURL: this.serverUrl,
            json: true,
        };
        if (body) {
            if (method === 'GET') {
                requestOptions['params'] = body;
            } else {
                requestOptions['data'] = body;
            }
        }

        return new Promise((resolve, reject) => {
            var startTime = new Date();
            axios(requestOptions).then((res) => {
                resolve(new ApiResponse(res, new Date - startTime));
            }).catch((e) => {
                if (e.response === undefined) {
                    return reject(e);
                }

                var {data} = e.response;
                var totalTime = new Date - startTime;
                var response = new ApiResponse(data, totalTime);

                if (data.status !== undefined) {
                    resolve(response);
                } else {
                    reject(response);
                }
            });
        });
    }
}


var endpointMap = {
    mensagens: 'mensagens',
    modelos: 'modelos',
    veiculos: 'veiculos',
    version: 'version'
};

var methods = [
    '',
    'Get',
    'Post',
    'Put',
    'Patch',
    'Delete'
];

for (let endpoint in endpointMap) {
    methods.forEach((method) => {
        ApiClient.prototype[endpoint + method] = function (body, path) {
            if (path !== undefined) {
                path = endpoint + '/' + path;
            } else {
                path = endpoint;
            }
            return this.apiCall(method, path, body);
        };
    });
}

export default ApiClient;
