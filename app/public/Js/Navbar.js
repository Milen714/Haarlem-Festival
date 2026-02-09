
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
           Language Selector
        ------------------------------ */
        const languageOptions = document.querySelectorAll(".language-option");
        const selectedLanguage = document.getElementById("selectedLanguage");
        const languageDropdown = document.getElementById("languageDropdown");
        const languageButton = document.querySelector("[data-dropdown-toggle='languageDropdown']");

        languageOptions.forEach(option => {
            option.addEventListener("click", (e) => {
                e.stopPropagation();
                const lang = option.getAttribute("data-lang");
                if (selectedLanguage) {
                    selectedLanguage.textContent = lang;
                }
                // Update the flag icon in the button
                const flagImg = option.querySelector("img");
                const buttonImg = languageButton?.querySelector("img");
                if (flagImg && buttonImg) {
                    buttonImg.src = flagImg.src;
                    buttonImg.alt = flagImg.alt;
                }
                // Close the dropdown
                if (languageDropdown) {
                    languageDropdown.classList.add("hidden");
                    languageButton?.setAttribute("aria-expanded", "false");
                }
                // Store selection (you can also send to server here)
                localStorage.setItem("selectedLanguage", lang);
            });
        });

        // Restore saved language on load
        const savedLang = localStorage.getItem("selectedLanguage");
        if (savedLang && selectedLanguage) {
            selectedLanguage.textContent = savedLang;
            const savedOption = document.querySelector(`.language-option[data-lang="${savedLang}"]`);
            const flagImg = savedOption?.querySelector("img");
            const buttonImg = languageButton?.querySelector("img");
            if (flagImg && buttonImg) {
                buttonImg.src = flagImg.src;
                buttonImg.alt = flagImg.alt;
            }
        }

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

        /* ------------------------------
           Close dropdowns on ESC key
        ------------------------------ */
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                dropdownButtons.forEach(button => {
                    const dropdownId = button.getAttribute("data-dropdown-toggle");
                    const dropdown = document.getElementById(dropdownId);
                    if (dropdown) {
                        dropdown.classList.add("hidden");
                        button.setAttribute("aria-expanded", "false");
                    }
                });
            }
        });
    });
