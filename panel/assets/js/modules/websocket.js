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

        if (data.function === 'message' && data.chatUsername && data.message && (data.self == true || data.self == false) && (data.isLoveTesterResult == true || data.isLoveTesterResult == false) && data.createdAt) {
            if(typeof chats !== "undefined"){
                let utcDate = new Date(data.createdAt + 'Z');
                let localDate = utcDate.toLocaleString(undefined, { 
                    year: 'numeric', month: '2-digit', day: '2-digit', 
                    hour: '2-digit', minute: '2-digit', second: '2-digit', 
                    hour12: false 
                }).replace(',', '');

                chats.addMessage(data.chatUsername, data.message, data.self, data.isLoveTesterResult, localDate);
            }else if(data.self === true && data.isLoveTesterResult == true){
                notifications.show('Je liefdestest resultaat is gedeeld!', 'success');
            }else if(data.self === false){
                sounds.newMessage();
            }

            if(data.self === true){
                sounds.sendMessage();
            }
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

export function sendLoveTesterResult(to, historyId){
    if (socket && socket.readyState === WebSocket.OPEN) {
        socket.send(JSON.stringify({
            function: 'sendLoveTesterResult',
            to: to,
            historyId: historyId
        }));
    } else {
        console.warn('WebSocket is niet open. Bericht niet verzonden:', message);
    }
}
