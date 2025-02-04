let defaultFunctions = null;

async function loadDefaultFunctions() {
    if(defaultFunctions === null) {
        defaultFunctions = await import(`../defaultFunctions.js?t=${new Date().getTime()}`);
    }
}

export async function onClickContact(element) {
    await loadDefaultFunctions();
    const userId = element.getAttribute('data-id');
    document.querySelector('.chat-container .messages').innerHTML = '';
    chats.setNewMessages(userId);
    console.log(userId);
}

export async function addContact(username) {
    await loadDefaultFunctions();
    defaultFunctions.fetchData({
        function: 'addContact',
        username: username
    }).then(data => {
        if (data.success) {
            const contactList = document.querySelector('.chat-list');
            const contact = document.createElement('div');
            contact.classList.add('chat-item');
            contact.addEventListener('click', function () {
                contacts.onClickContact(this);
            });
            contact.setAttribute('data-id', data.contactId);
            contact.setAttribute('data-username', username);
            contact.innerHTML = username;

            contactList.appendChild(contact);
            notifications.show('De gebruiker is toegevoegd aan de contactenlijst.');
        } else {
            notifications.show(data.error, "error");
        }
    });
}