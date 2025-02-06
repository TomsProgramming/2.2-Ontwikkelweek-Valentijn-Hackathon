let serviceWorker, notifications, websocket, headerNav, chats;

document.addEventListener('DOMContentLoaded', async function () {
    serviceWorker = await import(`./modules/serviceWorker.js?t=${new Date().getTime()}`);
    notifications = await import(`./modules/notifications.js?t=${new Date().getTime()}`);
    websocket = await import(`./modules/websocket.js?t=${new Date().getTime()}`);
    headerNav = await import(`./modules/headerNav.js?t=${new Date().getTime()}`);
    chats = await import(`./modules/chats.js?t=${new Date().getTime()}`);

    document.querySelector('.sidebar .add-chat').addEventListener('click', function () {
        document.querySelector('#addChatMenu').style.display = document.querySelector('#addChatMenu').style.display === "block" ? "none" : "block";
    });

    document.querySelector('.add-chat-menu .close').addEventListener('click', function () {
        document.querySelector('#addChatMenu').style.display = "none";
    });

    document.querySelector('.add-chat-menu .add-chat').addEventListener('click', function () {
        const username = document.querySelector('.add-chat-menu .username').value;
        document.querySelector('#addChatMenu').style.display = "none";
        chats.addChat(username);
    });

    document.querySelector('.chat-container .send').addEventListener('click', function () {
        const message = document.querySelector('.chat-container .input-container .message').value;
        document.querySelector('.chat-container .input-container .message').value = '';
        chats.sendMessage(message);
    });
});


function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('show');
    document.querySelector('.overlay').classList.toggle('show');
}

function closeSidebar() {
    document.querySelector('.sidebar').classList.remove('show');
    document.querySelector('.overlay').classList.remove('show');
}

function openChatAndCloseSidebar(el) {
    chats.openChat(el);
    closeSidebar();
}

function openAddChatMenu() {
    document.getElementById('addChatMenu').style.display = 'block';
}

function closeAddChatMenu() {
    document.getElementById('addChatMenu').style.display = 'none';
}

function closeProfileMenu() {
    document.querySelector('.profileContainer').style.display = 'none';
}