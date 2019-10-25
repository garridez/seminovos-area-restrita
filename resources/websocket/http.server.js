import http from 'http';
import fs from 'fs';

export default function () {
    var server = http.createServer(function (req, res) {
        fs.readFile('./index.html', 'utf-8', function (error, content) {
            res.writeHead(200, {"Content-Type": "text/html"});
            res.end(content);
        });
    });

    server.listen(5000);
    console.log('Node Webserver listening port 5000');
    return server;
};