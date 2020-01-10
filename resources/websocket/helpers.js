/**
 * @todo refatorar essas funções
 */
var redis = require('redis');

var helpers = {
    getKeyOnRedis(key) {
        return new Promise(function (resolve) {
            var redisClient = redis.createClient({
                url: process.env.SESSION_SAVE_PATH.replace('tcp', 'redis')
            });
            redisClient.get(key, function (key, data) {
                redisClient.quit();
                resolve(data);
            });
        });
    },
    getIdCadastroBySession(idSession) {
        return new Promise((resolve, reject) => {
            idSession = 'PHPREDIS_SESSION:' + idSession;
            return helpers.getKeyOnRedis(idSession).then((data) => {
                if (data) {
                    var res = (data.match(/LOGIN_SESSION.*?idCadastro";i:(?<idCadastro>[0-9]+)/i));
                    if (res) {
                        resolve(res.groups.idCadastro);
                    } else {
                        console.log('idCadastro not matched', data);
                        console.log(idSession);
                    }
                } else {
                    resolve(data);
                }
            });
        });
    }
};

module.exports = helpers;