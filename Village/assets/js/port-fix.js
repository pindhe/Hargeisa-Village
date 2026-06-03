/**
 * Cursor / preview often opens localhost:8081 — that is NOT the PHP site.
 * XAMPP Village runs on http://localhost/Village/ (port 80).
 */
(function () {
    'use strict';

    var host = window.location.hostname;
    var port = window.location.port;

    if (host !== 'localhost' && host !== '127.0.0.1') {
        return;
    }

    if (port !== '8081' && port !== '8080') {
        return;
    }

    var target = 'http://' + host + ':80' + window.location.pathname + window.location.search + window.location.hash;
    window.location.replace(target);
})();
