document.addEventListener("DOMContentLoaded", function () {

    const nav = document.querySelector(".navigationBar");
    const toggleBtn = document.getElementById("menuToggle");
    const userMenu = document.querySelector(".navigationBarListUser");

    if (!nav || !toggleBtn || !userMenu) return;

    /* ===== TOGGLE ===== */
    toggleBtn.addEventListener("click", function (e) {
        e.preventDefault();
        userMenu.classList.toggle("open");
    });

    /* ===== CERRAR SI CLICK FUERA DEL NAV ===== */
    document.addEventListener("click", function (e) {

        if (!nav.contains(e.target)) {
            userMenu.classList.remove("open");
        }

    });

});