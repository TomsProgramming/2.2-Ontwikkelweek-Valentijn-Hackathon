let swRegistration;
let defaultFunctions = null;
let isSubscribed = false;

async function loadDefaultFunctions() {
    if (defaultFunctions === null) {
        defaultFunctions = await import(`../defaultFunctions.js?t=${new Date().getTime()}`);
    }
}

function urlB64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; i++) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

export async function subscribeUser() {
    await loadDefaultFunctions();

    await unsubscribeUser();
    const applicationServerKey = urlB64ToUint8Array('BFskdHv-zYi_7eS7VXDcfEKKZCDMzHeYVjg3uuUhmp5IaLjyUNuQyrZJbkfnjjGYUw99jVbHvu7wC-WcgJaeHe8');
    swRegistration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: applicationServerKey
    })
        .then(function (subscription) {
            console.log('User is subscribed.');
            isSubscribed = true;

            defaultFunctions.fetchData({
                function: 'updateNotificationData',
                setNotification: true,
                subscription: subscription
            }).then(data => {
                if (!data.success) {
                    console.log(`Failed to update the user: ${data.error}`);
                }
            });
        })
        .catch(function (err) {
            console.log(`Failed to subscribe the user: ${err}`);
        });
}

export function unsubscribeUser() {
    swRegistration.pushManager.getSubscription()
        .then(function (subscription) {
            if (subscription) {
                return subscription.unsubscribe();
            }
        })
        .catch(function (error) {
            console.log('Error unsubscribing', error);
        })
        .then(function () {
            console.log('User is unsubscribed.');
            isSubscribed = false;
        });
}

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('../../../service-worker.js?v=' + serviceWorkerVersion)
        .then(registration => {
            console.log('Service Worker geregistreerd met scope:', registration.scope);
        })
        .catch(error => {
            console.error('Service Worker registratie mislukt:', error);
        });
}

navigator.serviceWorker.ready.then(function (registration) {
    swRegistration = registration;
    registration.pushManager.getSubscription()
        .then(function (subscription) {
            if (!subscription) {
                console.log('User is not subscribed.');
            } else {
                console.log('User is already subscribed.');
            }
        });
});