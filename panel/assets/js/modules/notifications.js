export function show(message, type = "success", duration = 5000) {
    const container = document.getElementById("notification-container");
    if (!container) {
        console.error("Geen #notification-container gevonden in de HTML.");
        return;
    }

    const notification = document.createElement("div");
    notification.classList.add("notification");

    if (type === "error") {
        notification.classList.add("error");
    }

    const messageElement = document.createElement("span");
    messageElement.textContent = message;

    const closeBtn = document.createElement("button");
    closeBtn.textContent = "✖";
    closeBtn.setAttribute("aria-label", "Sluiten");

    closeBtn.addEventListener("click", () => {
        close(notification);
    });

    notification.appendChild(messageElement);
    notification.appendChild(closeBtn);

    container.appendChild(notification);

    setTimeout(() => {
        close(notification);
    }, duration);
}

function close(notification) {
    notification.classList.add("hide");

    setTimeout(() => {
        if (notification && notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 500);
}
