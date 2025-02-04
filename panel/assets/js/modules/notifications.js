let defaultFunctions = null;

async function loadDefaultFunctions() {
    if(defaultFunctions === null) {
        defaultFunctions = await import(`../defaultFunctions.js?t=${new Date().getTime()}`);
    }
}

export async function show(message, type = "success", duration = 5000) {
    await loadDefaultFunctions();

    const container = document.getElementById("notification-container");
    const notification = document.createElement("div");
    notification.classList.add("notification");
    if (type === "error") {
        notification.classList.add("error");
    }

    notification.innerHTML = `
        <span>${message}</span>
        <button onclick="closeNotification(this)">✖</button>
    `;

    container.appendChild(notification);

    setTimeout(() => {
        closeNotification(notification);
    }, duration);
}

function closeNotification(element) {
    const notification = element.closest(".notification");
    notification.classList.add("hide");
    setTimeout(() => notification.remove(), 500);
}