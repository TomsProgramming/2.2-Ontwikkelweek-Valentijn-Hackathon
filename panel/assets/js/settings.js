let defaultFunctions, notifications, sounds, serviceWorker, websocket, headerNav, showcase;

document.addEventListener('DOMContentLoaded', async function () {
    defaultFunctions = await import(`./defaultFunctions.js?t=${new Date().getTime()}`);
    notifications = await import(`./modules/notifications.js?t=${new Date().getTime()}`);
    sounds = await import(`./modules/sounds.js?t=${new Date().getTime()}`);
    serviceWorker = await import(`./modules/serviceWorker.js?t=${new Date().getTime()}`);
    websocket = await import(`./modules/websocket.js?t=${new Date().getTime()}`);
    headerNav = await import(`./modules/headerNav.js?t=${new Date().getTime()}`);
    showcase = await import(`./modules/showcase.js?t=${new Date().getTime()}`);

    const notificationSubscription = await serviceWorker.checkSubscription();

    const notificationInput = document.getElementById('notifications');
    notificationInput.checked = notificationSubscription;

    document.querySelector('#soundTestNotification').addEventListener('click', function () {
        const selectedSound = document.querySelector('#notification-sound').value;
    
        if (!selectedSound || selectedSound === "nothing") {
            return;
        }

        const audio = new Audio(`../sounds/notifications/${selectedSound}.mp3`);
        audio.play().catch(error => notifications.show('Er is een fout opgetreden bij het afspelen van het geluid', 'error'));
    });
    
    document.querySelector('#soundTestSendMessage').addEventListener('click', function () {
        const selectedSound = document.querySelector('#send-sound').value;

        if (!selectedSound || selectedSound === "nothing") {
            return;
        }

        const audio = new Audio(`../sounds/sendMessage/${selectedSound}.mp3`);
        audio.play().catch(error => notifications.show('Er is een fout opgetreden bij het afspelen van het geluid', 'error'));
    });

    document.querySelector('.settings-actions .save-button').addEventListener('click', async function () {
        const usernameInput = document.getElementById('username');
        const username = usernameInput.value.trim();

        if(username !== ""){
            const changeUsername = await defaultFunctions.fetchData({
                function: 'changeUsername',
                username: username
            });
            console.log(changeUsername);
            if (changeUsername.success) {
                usernameInput.value = '';
                notifications.show('Gebruikersnaam succesvol gewijzigd');
            } else {
                notifications.show(changeUsername.error, 'error');
            }
        }

        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value; 
        const confirmPassword = document.getElementById('confirm_new_password').value;

        if(newPassword !== "" && confirmPassword !== "" && currentPassword !== ""){
            const changePassword = await defaultFunctions.fetchData({
                function: 'changePassword',
                currentPassword: currentPassword,
                newPassword: newPassword,
                confirmPassword: confirmPassword
            });

            if (changePassword.success) {
                document.getElementById('current_password').value = '';
                document.getElementById('new_password').value = '';
                document.getElementById('confirm_new_password').value = '';
                window.location.href = "/";
            } else {
                notifications.show(changePassword.error, 'error');
            }
        }

        const notificationsChecked = document.getElementById('notifications').checked;
        if(notificationsChecked){
            await serviceWorker.subscribeUser();
        }else{
            await serviceWorker.unsubscribeUser();
        }

        const selectedNotificationSound = document.querySelector('#notification-sound').value;
        const selectedSendSound = document.querySelector('#send-sound').value;
        const backgroundSound = document.querySelector('#background-music').checked;
        const changeSound = await defaultFunctions.fetchData({
            function: 'changeSound',
            notificationSound: selectedNotificationSound,
            sendSound: selectedSendSound,
            backgroundSound: backgroundSound
        });

        if (changeSound.success) {
            notifications.show('Notificatie geluid succesvol gewijzigd');
        } else {
            notifications.show(changeSound.error, 'error');
        }
    });
});