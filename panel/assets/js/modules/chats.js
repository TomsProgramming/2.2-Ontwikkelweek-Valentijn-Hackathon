let lastMessageDateTime = null;
let currentChatUsername = null;

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function updateChat(username, notifications, lastMessage, lastMessageDateTime, isLoveTesterResult) {
    let getChat = document.querySelector(`.chat-list .chat-item[data-username="${username}"]`);
    if (getChat) {
        getChat.querySelector('.unreadedMessages').style.display = notifications > 0 ? 'block' : 'none';
        getChat.querySelector('.unreadedMessages').innerHTML = notifications;
        if(isLoveTesterResult){
            const loveTesterData = JSON.parse(lastMessage);
            getChat.querySelector('.last-message').innerHTML = `${loveTesterData.name1} &amp; ${loveTesterData.name2} - ${loveTesterData.percentage}%`;
        }else{
            getChat.querySelector('.last-message').innerHTML = lastMessage;
        }
        getChat.querySelector('.time').innerHTML = lastMessageDateTime;
    }
}

async function createNewMessage(chatContainer, message, self, isLoveTesterResult) {
    const messageElement = document.createElement('div');
    messageElement.classList.add('message', self ? 'sender' : 'receiver');

    const messageContent = document.createElement('div');
    messageContent.classList.add('bubble');

    if (!isLoveTesterResult) {
        messageContent.innerHTML = message;

        messageElement.appendChild(messageContent);
        chatContainer.appendChild(messageElement);
    } else {
        try {
            const loveTesterData = JSON.parse(message);
            const percentage = parseInt(loveTesterData.percentage, 10);

            messageContent.setAttribute('name1', loveTesterData.name1);
            messageContent.setAttribute('name2', loveTesterData.name2);
            messageContent.style.setProperty('--percentage', percentage); // Unieke variabele per bericht

            messageContent.innerHTML = `
                <div class="heart">
                    <svg class="heartSvg" viewBox="0 0 100 100">
                        <defs>
                            <clipPath id="heartClip-${percentage}">
                                <rect class="clipRect" x="10" y="90" width="80" height="0"></rect>
                            </clipPath>
                        </defs>
                        <path d="M10,30 A20,20 0 0,1 50,30 A20,20 0 0,1 90,30 Q90,60 50,90 Q10,60 10,30 Z" 
                              fill="#FB7F8D" stroke="black" stroke-width="2" />
                        <path class="redFill" d="M10,30 A20,20 0 0,1 50,30 A20,20 0 0,1 90,30 Q90,60 50,90 Q10,60 10,30 Z" 
                              fill="red" stroke="black" stroke-width="2" clip-path="url(#heartClip-${percentage})" />
                        <text x="50" y="50" class="percentageText" text-anchor="middle" dy="5">0%</text>
                    </svg>
                    <p>${loveTesterData.name1} &amp; ${loveTesterData.name2}</p>
                </div>
            `;

            messageElement.appendChild(messageContent);
            chatContainer.appendChild(messageElement);

            await sleep(150);
            animatePercentage(messageContent, percentage);
        } catch (error) {
            console.error("Invalid JSON data for Love Tester:", error);
            messageContent.textContent = "Error: Invalid Love Tester Data";
        }
    }
}

function animatePercentage(messageContent, percentage) {
    const percentDisplay = messageContent.querySelector('.percentageText');
    let currentPercentage = 0;
    let startTime = null;

    function update(timestamp) {
        if (!startTime) startTime = timestamp;
        const progress = (timestamp - startTime) / 1000;

        currentPercentage = Math.min(Math.floor(progress * percentage), percentage);
        percentDisplay.textContent = `${currentPercentage}%`;

        if (currentPercentage < percentage) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}


export async function setNewMessages(contactUsername) {
    const messagesData = await defaultFunctions.fetchData({
        function: 'getMessages',
        contactUsername: contactUsername,
    });

    if (messagesData.success) {
        currentChatUsername = contactUsername;
        const chatContainer = document.querySelector('.chat-container .messages');
        chatContainer.innerHTML = '';
        messagesData.messages.forEach(message => {
            createNewMessage(chatContainer, message.message, message.type === 'sent', message.isLoveTesterResult === 1, true);
        });
        lastMessageDateTime = messagesData.messages.length > 0 ? messagesData.messages[messagesData.messages.length - 1].createdAt : null;
        updateChat(contactUsername, 0, messagesData.messages.length > 0 ? messagesData.messages[messagesData.messages.length - 1].message : '', lastMessageDateTime, messagesData.messages.length > 0 ? messagesData.messages[messagesData.messages.length - 1].isLoveTesterResult === 1 : false);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
}

export async function sendMessage(message) {
    if (currentChatUsername === null) {
        return;
    }
    websocket.sendMessage(message, currentChatUsername);
}

export async function addChat(username) {
    const usernameData = await defaultFunctions.fetchData({
        function: 'validUsernameCheck',
        username: username
    });

    if (usernameData.success) {
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
        avatarImage.setAttribute('src', `../uploads/profilePictures/${usernameData.contactId}.png`);

        const usernameElement = document.createElement('span');
        usernameElement.classList.add('username');
        usernameElement.innerHTML = username;

        const unreadedMessagesElement = document.createElement('span');
        unreadedMessagesElement.classList.add('unreadedMessages');
        unreadedMessagesElement.innerHTML = usernameData.chatNotifications;
        unreadedMessagesElement.style.display = usernameData.chatNotifications > 0 ? 'block' : 'none';

        headerElement.appendChild(avatarImage);
        headerElement.appendChild(usernameElement);
        headerElement.appendChild(unreadedMessagesElement);

        contactElement.appendChild(headerElement);

        const contentElement = document.createElement('div');
        contentElement.classList.add('content');

        const lastMessageElement = document.createElement('span');
        lastMessageElement.classList.add('last-message');
        lastMessageElement.innerHTML = usernameData.lastMessage != false ? usernameData.lastMessage.message : '';

        contentElement.appendChild(lastMessageElement);
        contactElement.appendChild(contentElement);

        const footerElement = document.createElement('div');
        footerElement.classList.add('footer');

        const timeElement = document.createElement('span');
        timeElement.classList.add('time');
        timeElement.innerHTML = usernameData.lastMessage != false ? usernameData.lastMessage.createdAt : '';

        footerElement.appendChild(timeElement);
        contactElement.appendChild(footerElement);

        contactList.appendChild(contactElement);
    } else {
        notifications.show(usernameData.error, "error");
    }
}

export async function addMessage(chatUsername, message, self, isLoveTesterResult, createdAt) {
    if (chatUsername === currentChatUsername) {
        const chatContainer = document.querySelector('.chat-container .messages');
        await createNewMessage(chatContainer, message, self, isLoveTesterResult);
        if (!self) {
            defaultFunctions.fetchData({
                function: 'readMessages',
                contactUsername: chatUsername,
            }).then(data => {
                if (!data.success) {
                    notifications.show(data.error, "error");
                }
            });
        }

        chatContainer.scrollTop = chatContainer.scrollHeight;
        await updateChat(chatUsername, 0, message, createdAt, isLoveTesterResult);
    } else if (!self) {
        let getChat = document.querySelector(`.chat-list .chat-item[data-username="${chatUsername}"]`);
        if (getChat) {
            await updateChat(chatUsername, parseInt(getChat.querySelector('.unreadedMessages').innerHTML) + 1, message, createdAt, isLoveTesterResult);
        } else {
            await addChat(chatUsername);
        }
        sounds.newMessage();
    }

    const chatList = document.querySelector('.chat-list');
    const chatItem = document.querySelector(`.chat-list .chat-item[data-username="${chatUsername}"]`)

    if (chatItem && chatList.firstChild !== chatItem) {
        chatList.prepend(chatItem);
    }
}

export async function openChat(usernameElement) {
    usernameElement.classList.add('active');
    if(currentChatUsername !== null){
        document.querySelector(`.chat-list .chat-item[data-username="${currentChatUsername}"]`).classList.remove('active');
    }

    const username = usernameElement.getAttribute('data-username');
    document.querySelector('.chat-container .messages').innerHTML = '';
    setNewMessages(username);
    mobileHeader.close();
}

function reloadMessages() {
    if (currentChatUsername !== null) {
        const usernameElement = document.querySelector(`.chat-list .chat-item[data-username="${currentChatUsername}"]`);
        openChat(usernameElement);
    }
}

document.querySelector('.sidebar .add-chat').addEventListener('click', function () {
    document.querySelector('#addChatMenu').style.display = 'block';
    document.querySelector('#addChatMenuOverlay').style.display = 'block';
});

document.querySelector('.add-chat-menu .close').addEventListener('click', function () {
    document.querySelector('#addChatMenu').style.display = "none";
    document.querySelector('#addChatMenuOverlay').style.display = 'none';
});

document.querySelector('.add-chat-menu .add-chat').addEventListener('click', function () {
    const username = document.querySelector('.add-chat-menu .username').value;
    document.querySelector('#addChatMenu').style.display = "none";
    document.querySelector('#addChatMenuOverlay').style.display = 'none';
    chats.addChat(username);
    document.querySelector('.add-chat-menu .username').value = '';
});

document.querySelector('#addChatMenuOverlay').addEventListener('click', function () {
    document.querySelector('#addChatMenu').style.display = "none";
    document.querySelector('#addChatMenuOverlay').style.display = 'none';
});

document.querySelector('.chat-container .input-container').addEventListener('submit', function (e) {
    e.preventDefault();

    const message = document.querySelector('.chat-container .input-container .message').value;
    document.querySelector('.chat-container .input-container .message').value = '';
    chats.sendMessage(message);
});

document.addEventListener('visibilitychange', function () {
    reloadMessages();
});