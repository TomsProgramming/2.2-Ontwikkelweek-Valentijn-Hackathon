document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('#auth-form').addEventListener('submit', function (e) {
        e.preventDefault();
        
        fetch("assets/php/functions.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                function: "register",
                username: document.querySelector("#auth-form #username").value,
                email: document.querySelector("#auth-form #email").value,
                password: document.querySelector("#auth-form #password").value,
                passwordCopy: document.querySelector("#auth-form #passwordCopy").value,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server error: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.href = "../index.php";
            } else {
                notification('error', data.error);
            }
        })
        .catch(err => {
            console.error("Error:", err);
            notification('error', "Er is een fout opgetreden. Probeer het later opnieuw.");
        });
    });
});