let defaultFunctions = null;
let lastMessageDateTime = null;
let currentChatUsername = null;

async function loadDefaultFunctions() {
    if(defaultFunctions === null) {
        defaultFunctions = await import(`../defaultFunctions.js?t=${new Date().getTime()}`);
    }
}

function updateChat(username, notifications, lastMessage, lastMessageDateTime){
    if(lastMessageDateTime === undefined){
        lastMessageDateTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
    }
    let getChat = document.querySelector(`.chat-list .chat-item[data-username="${username}"]`);
    if(getChat){
        getChat.querySelector('.unreadedMessages').style.display = notifications > 0 ? 'block' : 'none';
        getChat.querySelector('.unreadedMessages').innerHTML = notifications;
        getChat.querySelector('.last-message').innerHTML = lastMessage;
        getChat.querySelector('.time').innerHTML = lastMessageDateTime;
    }
}

export async function setNewMessages(contactUsername){
    await loadDefaultFunctions();
    defaultFunctions.fetchData({
        function: 'getMessages',
        contactUsername: contactUsername,
    }).then(data => {
        if(data.success){
            currentChatUsername = contactUsername;
            const chatContainer = document.querySelector('.chat-container .messages');
            chatContainer.innerHTML = '';
            data.messages.forEach(message => {
                const messageElement = document.createElement('div');
                messageElement.classList.add('message');
                messageElement.classList.add(message.type === 'received' ? 'receiver' : 'sender');

                const messageContent = document.createElement('div');
                messageContent.classList.add('bubble');
                messageContent.innerHTML = message.message;
                messageElement.appendChild(messageContent);

                chatContainer.appendChild(messageElement);
            });
            lastMessageDateTime = data.messages.length > 0 ? data.messages[data.messages.length - 1].createdAt : null;
            updateChat(contactUsername, 0, data.messages.length > 0 ? data.messages[data.messages.length - 1].message : '', lastMessageDateTime);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
}

export async function sendMessage(message){
    await loadDefaultFunctions();
    if(currentChatUsername === null){
        return;
    }
    websocket.sendMessage(message, currentChatUsername);

    const chatContainer = document.querySelector('.chat-container .messages');
    const messageElement = document.createElement('div');
    messageElement.classList.add('message');
    messageElement.classList.add('sender');

    const messageContent = document.createElement('div');
    messageContent.classList.add('bubble');
    messageContent.innerHTML = message;
    messageElement.appendChild(messageContent);

    chatContainer.appendChild(messageElement);

    updateChat(currentChatUsername, 0, message);
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

export async function addChat(username) {
    await loadDefaultFunctions();
    defaultFunctions.fetchData({
        function: 'validUsernameCheck',
        username: username
    }).then(data => {
        if (data.success) {
            const contactList = document.querySelector('.chat-list');
            const contactElement = document.createElement('div');
            contactElement.classList.add('chat-item');
            contactElement.setAttribute('onclick', 'chats.openChat(this)');
            contactElement.setAttribute('data-username', username);

            const headerElement = document.createElement('div');
            headerElement.classList.add('header');

            const avatarImage = document.createElement('img');
            avatarImage.classList.add('avatar');
            avatarImage.setAttribute('alt', 'Profile');
            avatarImage.setAttribute('src', `../uploads/profilePictures/${data.contactId}.png`);

            const usernameElement = document.createElement('span');
            usernameElement.classList.add('username');
            usernameElement.innerHTML = username;

            const unreadedMessagesElement = document.createElement('span');
            unreadedMessagesElement.classList.add('unreadedMessages');
            unreadedMessagesElement.innerHTML = data.chatNotifications;
            unreadedMessagesElement.style.display = data.chatNotifications > 0 ? 'block' : 'none';

            headerElement.appendChild(avatarImage);
            headerElement.appendChild(usernameElement);
            headerElement.appendChild(unreadedMessagesElement);

            contactElement.appendChild(headerElement);

            const contentElement = document.createElement('div');
            contentElement.classList.add('content');

            const lastMessageElement = document.createElement('span');
            lastMessageElement.classList.add('last-message');
            lastMessageElement.innerHTML = data.lastMessage != false ? data.lastMessage.message : '';
            
            contentElement.appendChild(lastMessageElement);
            contactElement.appendChild(contentElement);

            const footerElement = document.createElement('div');
            footerElement.classList.add('footer');

            const timeElement = document.createElement('span');
            timeElement.classList.add('time');
            timeElement.innerHTML = data.lastMessage != false ? data.lastMessage.createdAt : '';

            footerElement.appendChild(timeElement);
            contactElement.appendChild(footerElement);

            contactList.appendChild(contactElement);
        } else {
            notifications.show(data.error, "error");
        }
    });
}

export async function addMessage(senderUsername, message){
    await loadDefaultFunctions();
    
    if(senderUsername === currentChatUsername){
        const chatContainer = document.querySelector('.chat-container .messages');
        const messageElement = document.createElement('div');
        messageElement.classList.add('message');
        messageElement.classList.add('receiver');

        const messageContent = document.createElement('div');
        messageContent.classList.add('bubble');
        messageContent.innerHTML = message;
        messageElement.appendChild(messageContent);

        chatContainer.appendChild(messageElement);

        defaultFunctions.fetchData({
            function: 'readMessages',
            contactUsername: senderUsername,
        }).then(data => {
            if(!data.success){
                notifications.show(data.error, "error");
            }
        });

        chatContainer.scrollTop = chatContainer.scrollHeight;
    }else{
        let getChat = document.querySelector(`.chat-list .chat-item[data-username="${senderUsername}"]`);
        if(getChat){
            updateChat(senderUsername, parseInt(getChat.querySelector('.unreadedMessages').innerHTML) + 1, message);
        }else{
            addChat(senderUsername);
        }
    }
}

export async function openChat(usernameElement){
    await loadDefaultFunctions();
    const username = usernameElement.getAttribute('data-username');
    document.querySelector('.chat-container .messages').innerHTML = '';
    setNewMessages(username);
}