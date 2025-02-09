document.querySelector('.mobile-header .menu-toggle').addEventListener('click', function () {
    document.querySelector('.sidebar').classList.toggle('show');
    document.querySelector('.overlay').classList.toggle('show');
});

document.querySelector('#sideBarOverlay').addEventListener('click', function () {
    close();
});

export async function openChatAndClose(el) {
    chats.openChat(el);
    close();
}

export function close() {
    document.querySelector('.sidebar').classList.remove('show');
    document.querySelector('.overlay').classList.remove('show');
}