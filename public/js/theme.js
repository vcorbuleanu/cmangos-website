const change_theme = () => {
    const body = document.querySelector("body");
    const themes = ["alliance", "horde", "undead", "bloodelf", "tbc", "wotlk"];
    const theme = localStorage.getItem("theme") || "alliance";
    if (theme && themes.indexOf(theme) !== -1) {
        if (theme === "alliance") {
            localStorage.setItem("theme", "horde");
            body.classList.remove(...body.classList);
            body.classList.add("horde");
            return;
        }
        if (theme === "horde") {
            localStorage.setItem("theme", "undead");
            body.classList.remove(...body.classList);
            body.classList.add("undead");
            return;
        }
        if (theme === "undead") {
            localStorage.setItem("theme", "bloodelf");
            body.classList.remove(...body.classList);
            body.classList.add("bloodelf");
            return;
        }
        if (theme === "bloodelf") {
            localStorage.setItem("theme", "tbc");
            body.classList.remove(...body.classList);
            body.classList.add("tbc");
            return;
        }
        if (theme === "tbc") {
            localStorage.setItem("theme", "wotlk");
            body.classList.remove(...body.classList);
            body.classList.add("wotlk");
            return;
        }
        if (theme === "wotlk") {
            localStorage.setItem("theme", "alliance");
            body.classList.remove(...body.classList);
            body.classList.add("alliance");
            return;
        }
    }
};

document.addEventListener("DOMContentLoaded", () => {
    const logoutLink = document.querySelector("#logout");
    if (logoutLink) {
        logoutLink.addEventListener("click", function(event) {
            const message = "Are you sure you want to logout?";
            if (!window.confirm(message)) {
              event.preventDefault();
            }
        });
    }
    
    const body = document.querySelector("body");
    const themes = ["alliance", "horde", "undead", "bloodelf", "tbc", "wotlk"];
    const theme = localStorage.getItem("theme") || "alliance";
    body.classList.remove(...body.classList);
    body.classList.add(theme);
    
    if (/Mobi/i.test(navigator.userAgent) && !(/Desk/i.test(navigator.userAgent))) {
        let link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = "/public/css/mobile.css";
        document.head.appendChild(link);
    }
    
    Array.from(document.querySelectorAll(".menu > .button")).forEach((element) => {
        let state = localStorage.getItem("menu") !== null ? JSON.parse(localStorage.getItem("menu")) : {};
        if (state[element.id] === "off")
            element.classList.add("off");
    });

    Array.from(document.querySelectorAll(".menu > .button > .text")).forEach((element) => {
        element.addEventListener('click', () => {
            const parent = element.parentNode;
            let state = localStorage.getItem("menu") !== null ? JSON.parse(localStorage.getItem("menu")) : {};
            if (parent.classList.contains("off")) {
                parent.classList.remove("off");
                delete state[parent.id];
                localStorage.setItem("menu", JSON.stringify(state));
            } else {
                parent.classList.add("off");
                state[parent.id] = "off";
                localStorage.setItem("menu", JSON.stringify(state));
            }
        });
    });

    Array.from(document.querySelectorAll(".news")).forEach((element) => {
        let state = localStorage.getItem("news") !== null ? JSON.parse(localStorage.getItem("news")) : {};
        if (state[element.id] === "off")
            element.classList.add("off");
    });
    
    Array.from(document.querySelectorAll(".news > .header")).forEach((element) => {
        element.addEventListener('click', () => {
            const parent = element.parentNode;
            let state = localStorage.getItem("news") !== null ? JSON.parse(localStorage.getItem("news")) : {};
            if (parent.classList.contains("off")) {
                parent.classList.remove("off");
                delete state[parent.id];
                localStorage.setItem("news", JSON.stringify(state));
            } else {
                parent.classList.add("off");
                state[parent.id] = "off";
                localStorage.setItem("news", JSON.stringify(state));
            }
        });
    });
});