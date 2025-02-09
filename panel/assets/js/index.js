let defaultFunctions, notifications, sounds, serviceWorker, websocket, headerNav, loveTester, loveTesterShare, showcase;

document.addEventListener('DOMContentLoaded', async function () {
    defaultFunctions = await import(`./defaultFunctions.js?t=${new Date().getTime()}`);
    notifications = await import(`./modules/notifications.js?t=${new Date().getTime()}`);
    sounds = await import(`./modules/sounds.js?t=${new Date().getTime()}`);
    serviceWorker = await import(`./modules/serviceWorker.js?t=${new Date().getTime()}`);
    websocket = await import(`./modules/websocket.js?t=${new Date().getTime()}`);
    headerNav = await import(`./modules/headerNav.js?t=${new Date().getTime()}`);
    loveTester = await import(`./modules/loveTester.js?t=${new Date().getTime()}`);
    loveTesterShare = await import(`./modules/loveTesterShare.js?t=${new Date().getTime()}`);
    showcase = await import(`./modules/showcase.js?t=${new Date().getTime()}`);
});