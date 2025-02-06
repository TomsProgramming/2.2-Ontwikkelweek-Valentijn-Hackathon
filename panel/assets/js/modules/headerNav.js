document.querySelector('.nav-bar .menu-toggle').addEventListener('click', function () {
    var navLinks = document.getElementById("navLinks");
    if(navLinks){
        navLinks.classList.toggle("show");
    }
});