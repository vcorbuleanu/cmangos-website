const tooltip = {
    mouseX: -1,
    mouseY: -1,
    hovering: false,
    element: null,
    targets: null,
    process: () => {
        if (!/Mobi/i.test(navigator.userAgent) && tooltip.targets) {
            tooltip.hovering = false;
            tooltip.targets.forEach(target => {
                let bounds = target.getBoundingClientRect();
                if (tooltip.mouseX >= bounds.left && tooltip.mouseX <= bounds.right && tooltip.mouseY >= bounds.top && tooltip.mouseY <= bounds.bottom) {
                    tooltip.hovering = true;
                    if (!tooltip.element) {
                        tooltip.element = document.createElement("div");
                        tooltip.element.classList.add("tooltip");
                        document.body.appendChild(tooltip.element);
                    }
                    tooltip.element.textContent = target.getAttribute("data-tooltip");
                    tooltip.element.style.left = (tooltip.mouseX + window.scrollX) + 13*100/Math.round(window.devicePixelRatio * 100) + "px";
                    tooltip.element.style.top = (tooltip.mouseY + window.scrollY) + 23*100/Math.round(window.devicePixelRatio * 100) + "px";
                }
            });
    
            if (tooltip.element && !tooltip.hovering) {
                tooltip.element.remove();
                tooltip.element = null;
            }
        }
    }
};

window.onload = () => {
    tooltip.targets = document.querySelectorAll("[data-tooltip]");
    if (/Mobi/i.test(navigator.userAgent)) {
        tooltip.targets.forEach(target => {
            target.addEventListener("click", (e) => {
                if (!tooltip.element) {
                    tooltip.element = document.createElement("div");
                    tooltip.element.classList.add("tooltip");
                    document.body.appendChild(tooltip.element);
                }
                tooltip.element.textContent = target.getAttribute("data-tooltip");
                const zoom = window.orientation === 90 || window.orientation === -90 ? 1/0.8 : 1/0.4;
                tooltip.element.style.left = e.x * zoom + window.scrollX * zoom + "px";
                tooltip.element.style.top = e.y * zoom + window.scrollY * zoom + "px";
            });
            target.addEventListener("mouseleave", () => {
                tooltip.element.remove();
                tooltip.element = null;
            });
        });
    }
};

window.onscroll = () => {
    if (!/Mobi/i.test(navigator.userAgent))
        tooltip.process();
}

window.onmousemove = (e) => {
    if (!/Mobi/i.test(navigator.userAgent)) {
        tooltip.mouseX = e.clientX;
        tooltip.mouseY = e.clientY;
        tooltip.process();
    }
};