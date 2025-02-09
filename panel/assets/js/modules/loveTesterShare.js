let currentHistoryId = null;

export async function shareMenu(historyId){
    currentHistoryId = historyId;

    const chats = await defaultFunctions.fetchData({
        function: 'getChats'
    });

    if(chats.success == true){
        const chatList = document.querySelector('.share-menu .chat-list');
        chatList.innerHTML = '';

        for(const chat of chats.chats){
            const anthorId = Number(chat.senderId) == Number(chats.userId) ? chat.receiverId : chat.senderId;

            const chatItemElement = document.createElement('div');
            chatItemElement.classList.add('chat-item-share');

            const imageElement = document.createElement('img');
            imageElement.src = `../uploads/profilePictures/${anthorId}.png`;

            const nameElement = document.createElement('span');
            nameElement.classList.add('chat-username');
            nameElement.innerHTML = chat.username;

            const shareButton = document.createElement('button');
            shareButton.setAttribute('onclick', `loveTesterShare.shareHistory('${chat.username}')`);
            shareButton.innerHTML = 'Deel';

            chatItemElement.appendChild(imageElement);
            chatItemElement.appendChild(nameElement);
            chatItemElement.appendChild(shareButton);

            chatList.appendChild(chatItemElement);
        }
    }

    document.getElementById('shareMenu').style.display = 'block';
    document.getElementById('shareMenuOverlay').style.display = 'block';
}

function closeMenu(){
    document.getElementById('shareMenu').style.display = 'none';
    document.getElementById('shareMenuOverlay').style.display = 'none';
}

export async function shareHistory(chatUsername){
    websocket.sendLoveTesterResult(chatUsername, currentHistoryId);
    closeMenu();
}

document.querySelector('.share-menu-overlay').addEventListener('click', closeMenu);
document.querySelector('.share-menu .close-menu').addEventListener('click', closeMenu);