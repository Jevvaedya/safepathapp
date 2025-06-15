document.addEventListener("DOMContentLoaded", function () {
    const header = document.getElementById("mainWelcomeHeader");
    const headerLogo = document.getElementById("headerWelcomeLogo");

    const logoPutihSrc = "/images/safepath-logo-white.png";
    const logoUnguSrc = "/images/safepath-logo.png";

    const navLinkDashboard = document.getElementById("navLinkWelcomeDashboard");
    const navLinkLogin = document.getElementById("navLinkWelcomeLogin");
    const navLinkRegister = document.getElementById("navLinkWelcomeRegister");

    const navLinks = [navLinkDashboard, navLinkLogin, navLinkRegister].filter(
        (link) => link !== null
    );

    const scrollTriggerHeight = 50;

    if (header) {
        const handleScroll = () => {
            if (window.scrollY > scrollTriggerHeight) {
                header.classList.add("scrolled");
                if (headerLogo) {
                    headerLogo.src = logoUnguSrc;
                }
                navLinks.forEach((link) => {
                    link.classList.remove("text-white", "hover:text-gray-200");
                    // Warna teks saat header solid putih (kontras)
                    link.classList.add("text-text-main", "hover:text-primary");
                });
            } else {
                header.classList.remove("scrolled");
                if (headerLogo) {
                    headerLogo.src = logoPutihSrc;
                }
                navLinks.forEach((link) => {
                    link.classList.add("text-white", "hover:text-gray-200");
                    link.classList.remove(
                        "text-text-main",
                        "hover:text-primary"
                    );
                });
            }
        };

        handleScroll();
        window.addEventListener("scroll", handleScroll);
    } else {
        if (document.getElementById("hero")) {
            console.warn("Elemen header #mainWelcomeHeader tidak ditemukan.");
        }
    }
    
});
