let socket = null;

function getCookie(name) {
    let value = "; " + document.cookie;
    let parts = value.split("; " + name + "=");
    if (parts.length === 2) return parts.pop().split(";").shift();
    return null;
}

const token = getCookie('token');

function connectWithWebsocket() {
    if (socket && (socket.readyState === WebSocket.OPEN || socket.readyState === WebSocket.CONNECTING)) {
        return;
    }

    socket = new WebSocket('wss://valentijnhackathonwebsocket.tomtiedemann.com');

    socket.addEventListener('open', () => {
        socket.send(JSON.stringify({
            function: 'connect',
            token: token
        }));
    });

    socket.addEventListener('message', (event) => {
        const data = JSON.parse(event.data);
        if (data.function === 'connected') {
        }

        if (data.function === 'notification' && data.type && data.message) {
            notifications.show(data.message, data.type);
        }

        if (data.function === 'message' && data.from && data.message) {
            console.log('new message:', data);
            chats.addMessage(data.from, data.message);
        }
    });

    socket.addEventListener('close', () => {
        setTimeout(connectWithWebsocket, 1000);
    });

    socket.addEventListener('error', (error) => {
        console.error('WebSocket fout:', error);
    });
}

connectWithWebsocket();

document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        connectWithWebsocket();
    }
});
export function sendMessage(message, to) {
    if (socket && socket.readyState === WebSocket.OPEN) {
        socket.send(JSON.stringify({
            function: 'sendMessage',
            message: message,
            to: to
        }));
    } else {
        console.warn('WebSocket is niet open. Bericht niet verzonden:', message);
    }
}
