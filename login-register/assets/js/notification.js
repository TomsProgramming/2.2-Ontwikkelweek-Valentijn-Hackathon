function notification(type, message, duration = 5000) {
    var notification = document.querySelector(".notification");
    if (notification) {
        notification.textContent = message;
        notification.className = "notification " + type; 
        setTimeout(function() {
            notification.textContent = "";
            notification.className = "notification";
        }, duration);
    }
}