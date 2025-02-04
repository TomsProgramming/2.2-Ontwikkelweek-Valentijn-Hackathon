let socket;

function getCookie(name) {
    let value = "; " + document.cookie;
    let parts = value.split("; " + name + "=");
    if (parts.length === 2) return parts.pop().split(";").shift();
}

let token = getCookie('token');

socket = new WebSocket('wss://valentijnhackathonwebsocket.tomtiedemann.com');

socket.addEventListener('open', () => {
   socket.send(JSON.stringify({ function: 'connect', token: token }));
});

socket.addEventListener("message", (event) => {
    const data = JSON.parse(event.data);
    console.log(data);
    if(data.function === 'connected'){
        console.log('Connected to websocket server');
    }

    if(data.function === 'notification' && data.type && data.message){
        notifications.show(data.message, data.type);
    }

    if(data.function === 'newMessage' && data.message && data.from){
        console.log(data);
        chats.addMessage(data.from, data.message);
    }
});

export function sendMessage(message, to){
    socket.send(JSON.stringify({ function: 'sendMessage', message: message, to: to }));
}