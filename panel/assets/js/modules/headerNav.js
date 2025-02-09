document.querySelector('.nav-bar .menu-toggle').addEventListener('click', function () {
    var navLinks = document.getElementById("navLinks");
    if(navLinks){
        navLinks.classList.toggle("show");
    }
});

document.querySelector('.nav-bar .nav-right .profile-icon').addEventListener('click', function () {
    document.getElementById("profileDropdown").classList.toggle("show");
});

document.querySelector('.nav-bar .nav-left .logo').addEventListener('click', function () {
    window.location.href = "/";
});

window.addEventListener("click", function(event) {
    const dropdown = document.getElementById("profileDropdown");
    if (!event.target.closest(".profile-menu-container")) {
        dropdown.classList.remove("show");
    }
});