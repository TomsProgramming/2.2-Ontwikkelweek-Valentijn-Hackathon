let defaultFunctions = null;
let lastMessageDateTime = null;
let currentChatUserId = null;

async function loadDefaultFunctions() {
    if(defaultFunctions === null) {
        defaultFunctions = await import(`../defaultFunctions.js?t=${new Date().getTime()}`);
    }
}

export async function setNewMessages(contactId){
    await loadDefaultFunctions();
    currentChatUserId = contactId;
    defaultFunctions.fetchData({
        function: 'getMessages',
        contactId: contactId,
    }).then(data => {
        if(data.success){
            const chatContainer = document.querySelector('.chat-container .messages');
            chatContainer.innerHTML = '';
            data.messages.forEach(message => {
                const messageElement = document.createElement('div');
                messageElement.classList.add('message');
                console.log(message.senderId);
                console.log(contactId);
                console.log(parseInt(message.senderId) === parseInt(contactId));
                messageElement.classList.add(parseInt(message.senderId) === parseInt(contactId) ? 'receiver' : 'sender');

                const messageContent = document.createElement('div');
                messageContent.classList.add('bubble');
                messageContent.innerHTML = message.message;
                messageElement.appendChild(messageContent);

                chatContainer.appendChild(messageElement);
            });
            lastMessageDateTime = data.messages.length > 0 ? data.messages[data.messages.length - 1].createdAt : null;
            console.log("alles is ingeladen");
        }
    });
}

export async function sendMessage(message){
    await loadDefaultFunctions();
    if(currentChatUserId === null){
        return;
    }
    websocket.sendMessage(message, currentChatUserId);
    const chatContainer = document.querySelector('.chat-container .messages');
    const messageElement = document.createElement('div');
    messageElement.classList.add('message');
    messageElement.classList.add('sender');

    const messageContent = document.createElement('div');
    messageContent.classList.add('bubble');
    messageContent.innerHTML = message;
    messageElement.appendChild(messageContent);

    chatContainer.appendChild(messageElement);
}

export async function addMessage(senderId, message){
    await loadDefaultFunctions();
    console.log("dwaddwawda");
    if(parseInt(senderId) === parseInt(currentChatUserId)){
        console.log("dwaddwawddwadwadawwda");
        const chatContainer = document.querySelector('.chat-container .messages');
        const messageElement = document.createElement('div');
        messageElement.classList.add('message');
        messageElement.classList.add('receiver');

        const messageContent = document.createElement('div');
        messageContent.classList.add('bubble');
        messageContent.innerHTML = message;
        messageElement.appendChild(messageContent);

        chatContainer.appendChild(messageElement);
    }
}