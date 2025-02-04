document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('#auth-form').addEventListener('submit', function (e) {
        e.preventDefault();
        
        fetch("assets/php/functions.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                function: "verifyCode",
                code: document.querySelector("#auth-form #verificationCode").value
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

    document.querySelector('.resendVerificationCode').addEventListener('click', function () {
        fetch("assets/php/functions.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                function: "resendVerificationCode"
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
                notification('success', 'De verificatiecode is opnieuw verzonden.');
            } else {
                notification('error', data.error);
            }
        })
        .catch(err => {
            console.error("Error:", err);
            notification('error', "Er is een fout opgetreden. Probeer het later opnieuw.");
        });
    });

    document.querySelector('.logout').addEventListener('click', function () {
        fetch("assets/php/functions.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                function: "logout"
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