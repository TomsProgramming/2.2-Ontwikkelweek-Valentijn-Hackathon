let swRegistration;
let isSubscribed = false;

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
    swRegistration.pushManager.getSubscription()
        .then(function (subscription) {
            if (subscription) {
                return subscription;
            }
        })
        .catch(function (error) {
            console.log('Error fetching subscription', error);
        });
    
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
                notifications.show('Je bent aangemeld voor notificaties', 'success');
                if (!data.success) {
                    console.log(`Failed to update the user: ${data.error}`);
                }
            });
        })
        .catch(function (err) {
            notifications.show('Er is iets fout gegaan bij het aanmelden voor notificaties', 'error');
            console.log(`Failed to subscribe the user: ${err}`);
        });
}

export async function unsubscribeUser() {
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
            isSubscribed = false;

            defaultFunctions.fetchData({
                function: 'updateNotificationData',
                setNotification: false,
                subscription: []
            }).then(data => {
                if (!data.success) {
                    console.log(`Failed to update the user: ${data.error}`);
                }
            });
        });
}

export async function checkSubscription() {
    return isSubscribed;
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

navigator.serviceWorker.ready.then(async function (registration) {
    swRegistration = registration;
    registration.pushManager.getSubscription()
        .then(function (subscription) {
            if (!subscription) {
                isSubscribed = false;

                defaultFunctions.fetchData({
                    function: 'updateNotificationData',
                    setNotification: false,
                    subscription: []
                }).then(data => {
                    if (!data.success) {
                        console.log(`Failed to update the user: ${data.error}`);
                    }
                });

                console.log('User is not subscribed.');
            } else {
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

                console.log('User is already subscribed.');
            }
        });
});