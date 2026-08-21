(function (global) {
    var DEFAULT_ENDPOINT = 'https://admin.arrow.app.br/api/otp';

    function uuid() {
        if (global.crypto && typeof global.crypto.randomUUID === 'function') {
            return global.crypto.randomUUID();
        }
        var bytes = new Uint8Array(16);
        if (global.crypto && global.crypto.getRandomValues) {
            global.crypto.getRandomValues(bytes);
        } else {
            for (var i = 0; i < 16; i++) bytes[i] = Math.floor(Math.random() * 256);
        }
        bytes[6] = (bytes[6] & 0x0f) | 0x40;
        bytes[8] = (bytes[8] & 0x3f) | 0x80;
        var hex = Array.prototype.map.call(bytes, function (b) {
            return ('0' + b.toString(16)).slice(-2);
        }).join('');
        return hex.slice(0, 8) + '-' + hex.slice(8, 12) + '-' + hex.slice(12, 16) + '-' + hex.slice(16, 20) + '-' + hex.slice(20);
    }

    function postJson(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(body)
        }).then(function (res) {
            return res.json().then(function (json) {
                if (!res.ok || !json.success) {
                    throw new Error((json && json.message) || 'Falha no código de verificação.');
                }
                return json;
            });
        });
    }

    function showDisplayCode(code) {
        var el = document.getElementById('arrow-otp-display');
        if (!el || !code) return;
        el.style.display = 'block';
        el.innerHTML = 'Seu código neste aparelho: <strong style="letter-spacing:2px;font-size:1.25rem">' + code + '</strong><br><small>Enviado como notificação neste dispositivo. Não é SMS.</small>';
    }

    global.ArrowPhoneOtp = {
        endpoint: DEFAULT_ENDPOINT,
        newSessionId: uuid,
        showDisplayCode: showDisplayCode,
        send: function (phone, sessionId) {
            return postJson(this.endpoint + '/send', {
                phone: phone,
                sessionId: sessionId,
                fcmToken: ''
            }).then(function (json) {
                showDisplayCode(json.displayCode);
                return json;
            });
        },
        verify: function (phone, sessionId, code) {
            return postJson(this.endpoint + '/verify', {
                phone: phone,
                sessionId: sessionId,
                code: code
            }).then(function (json) {
                if (!json.customToken) {
                    throw new Error(json.message || 'Sessão Firebase inválida.');
                }
                return json.customToken;
            });
        }
    };
})(window);
