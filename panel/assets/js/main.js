let notifications, websocket, contacts, chats;

document.addEventListener('DOMContentLoaded', async function () {
    notifications = await import(`./modules/notifications.js?t=${new Date().getTime()}`);
    websocket = await import(`./modules/websocket.js?t=${new Date().getTime()}`);
    contacts = await import(`./modules/contacts.js?t=${new Date().getTime()}`);
    chats = await import(`./modules/chats.js?t=${new Date().getTime()}`);

    document.querySelector('.sidebar .add-user').addEventListener('click', function () {
        document.querySelector('#addUserMenu').style.display = document.querySelector('#addUserMenu').style.display === "block" ? "none" : "block";
    });

    document.querySelector('.add-user-menu .close').addEventListener('click', function () {
        document.querySelector('#addUserMenu').style.display = "none";
    });

    document.querySelector('.add-user-menu .add-user').addEventListener('click', function () {
        const username = document.querySelector('.add-user-menu .username').value;
        document.querySelector('#addUserMenu').style.display = "none";
        contacts.addContact(username);
    });

    document.querySelector('.chat-container .send').addEventListener('click', function () {
        const message = document.querySelector('.chat-container .input-container .message').value;
        document.querySelector('.chat-container .input-container .message').value = '';
        chats.sendMessage(message);
    });
});