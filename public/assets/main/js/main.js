(function () {
    "use strict";

    /* PRELOADER */
    window.addEventListener("load", function () {
        document.getElementById("preloader")?.remove();
    });

    /* HEADER SCROLL */
    const header = document.getElementById("siteHeader");

    function toggleHeader() {
        if (!header) return;
        window.scrollY > 80
            ? header.classList.add("scrolled")
            : header.classList.remove("scrolled");
    }

    window.addEventListener("scroll", toggleHeader);
    window.addEventListener("load", toggleHeader);

    /* SCROLL TOP */
    const scrollTopBtn = document.getElementById("scrollTop");

    function toggleScrollTop() {
        if (!scrollTopBtn) return;
        window.scrollY > 300
            ? scrollTopBtn.classList.remove("d-none")
            : scrollTopBtn.classList.add("d-none");
    }

    scrollTopBtn?.addEventListener("click", function (e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: "smooth" });
    });

    window.addEventListener("scroll", toggleScrollTop);
    window.addEventListener("load", toggleScrollTop);

    /* AOS */
    function initAOS() {
        if (typeof AOS !== "undefined") {
            AOS.init({
                duration: 600,
                easing: "ease-out",
                once: true,
                mirror: false,
                offset: 80,
            });
        }
    }

    window.addEventListener("load", initAOS);
})();
