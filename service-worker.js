self.addEventListener("install", (event) => {
    console.log("Service Worker geïnstalleerd.");
});

self.addEventListener("activate", (event) => {
    console.log("Service Worker geactiveerd.");
});

self.addEventListener("fetch", (event) => {
});

self.addEventListener('push', function(event) {
    const data = event.data.json(); 
    const options = {
        body: data.body,
        icon: 'icon.png',
        badge: 'badge.png'
    };
    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});
