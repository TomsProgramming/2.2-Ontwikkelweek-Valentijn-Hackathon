let serviceWorker, notifications, websocket, headerNav, loveTester;

loveTester = import(`./modules/loveTester.js?t=${new Date().getTime()}`);

document.addEventListener('DOMContentLoaded', async function () {
    serviceWorker = await import(`./modules/serviceWorker.js?t=${new Date().getTime()}`);
    notifications = await import(`./modules/notifications.js?t=${new Date().getTime()}`);
    websocket = await import(`./modules/websocket.js?t=${new Date().getTime()}`);
    headerNav = await import(`./modules/headerNav.js?t=${new Date().getTime()}`);
});