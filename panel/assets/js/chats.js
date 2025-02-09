let defaultFunctions, notifications, sounds, serviceWorker, websocket, headerNav, mobileHeader, chats, showcase;

document.addEventListener('DOMContentLoaded', async function () {
    defaultFunctions = await import(`./defaultFunctions.js?t=${new Date().getTime()}`);
    notifications = await import(`./modules/notifications.js?t=${new Date().getTime()}`);
    sounds = await import(`./modules/sounds.js?t=${new Date().getTime()}`);
    serviceWorker = await import(`./modules/serviceWorker.js?t=${new Date().getTime()}`);
    websocket = await import(`./modules/websocket.js?t=${new Date().getTime()}`);
    headerNav = await import(`./modules/headerNav.js?t=${new Date().getTime()}`);
    mobileHeader = await import(`./modules/mobileHeader.js?t=${new Date().getTime()}`);
    chats = await import(`./modules/chats.js?t=${new Date().getTime()}`);
    showcase = await import(`./modules/showcase.js?t=${new Date().getTime()}`);
});