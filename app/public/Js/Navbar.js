
    document.addEventListener("DOMContentLoaded", () => {
        /* ------------------------------
           Mobile menu toggle
        ------------------------------ */
        const menuToggleBtn = document.querySelector("[data-collapse-toggle]");
        const mobileMenu = document.getElementById(
            menuToggleBtn?.getAttribute("aria-controls")
        );

        if (menuToggleBtn && mobileMenu) {
            menuToggleBtn.addEventListener("click", () => {
                const isOpen = !mobileMenu.classList.contains("hidden");
                mobileMenu.classList.toggle("hidden");
                menuToggleBtn.setAttribute("aria-expanded", String(!isOpen));
            });
        }

        /* ------------------------------
           Dropdown toggles (all levels)
        ------------------------------ */
        const dropdownButtons = document.querySelectorAll("[data-dropdown-toggle]");

        dropdownButtons.forEach(button => {
            const dropdownId = button.getAttribute("data-dropdown-toggle");
            const dropdown = document.getElementById(dropdownId);

            if (!dropdown) return;

            button.addEventListener("click", (e) => {
                e.stopPropagation();

                // Close other open dropdowns at same level
                dropdownButtons.forEach(otherBtn => {
                    const otherId = otherBtn.getAttribute("data-dropdown-toggle");
                    const otherDropdown = document.getElementById(otherId);
                    if (otherDropdown && otherDropdown !== dropdown) {
                        otherDropdown.classList.add("hidden");
                        otherBtn.setAttribute("aria-expanded", "false");
                    }
                });

                dropdown.classList.toggle("hidden");
                const expanded = !dropdown.classList.contains("hidden");
                button.setAttribute("aria-expanded", String(expanded));

                /* Optional positioning for nested dropdown */
                if (button.dataset.dropdownPlacement === "right-start") {
                    dropdown.style.position = "absolute";
                    dropdown.style.left = "100%";
                    dropdown.style.top = "0";
                }
            });
        });

        /* ------------------------------
           Close dropdowns on outside click
        ------------------------------ */
        document.addEventListener("click", () => {
            dropdownButtons.forEach(button => {
                const dropdownId = button.getAttribute("data-dropdown-toggle");
                const dropdown = document.getElementById(dropdownId);
                if (dropdown) {
                    dropdown.classList.add("hidden");
                    button.setAttribute("aria-expanded", "false");
                }
            });
        });
    });
/* ------------------------------
           UI: User Menu Dropdown
        ------------------------------ */
    document.addEventListener("DOMContentLoaded", () => {
    const button = document.getElementById("userMenuButton");
    const dropdown = document.getElementById("userMenuDropdown");

    if (!button || !dropdown) return;

    // Toggle menu
    button.addEventListener("click", (e) => {
        e.stopPropagation();
        const isOpen = !dropdown.classList.contains("hidden");

        dropdown.classList.toggle("hidden");
        button.setAttribute("aria-expanded", String(!isOpen));
    });

    // Close on outside click
    document.addEventListener("click", (e) => {
        if (!dropdown.contains(e.target) && !button.contains(e.target)) {
            dropdown.classList.add("hidden");
            button.setAttribute("aria-expanded", "false");
        }
    });

    // Close on ESC
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            dropdown.classList.add("hidden");
            button.setAttribute("aria-expanded", "false");
        }
    });
});
