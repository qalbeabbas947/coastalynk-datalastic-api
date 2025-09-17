document.addEventListener("DOMContentLoaded", () => {
    const customSelects = document.querySelectorAll(".coastalynk-ddl-select");

    customSelects.forEach((customSelect) => {
        const selectButton = customSelect.querySelector(".coastalynk-ddl-button");
        const dropdown = customSelect.querySelector(".coastalynk-ddl-dropdown");
        const dropdown_wrapper = customSelect.querySelector(".coastalynk-ddl-dropdown-wrapper");
        const options = dropdown.querySelectorAll("li");
        const selectedValue = selectButton.querySelector(".coastalynk-ddl-selected-value");
        let drop_down_field = customSelect.querySelector(".coastalynk-ddl-dropdown-button-field");
        let focusedIndex = -1;
        
        const toggleDropdown = (expand = null) => {
            const isOpen = expand !== null ? expand : dropdown.classList.contains("hidden");
            dropdown.classList.toggle("hidden", !isOpen);
            dropdown_wrapper.classList.toggle("coastalynk-ddl-dropdown-wrapper-hidden", !isOpen);
            selectButton.setAttribute("aria-expanded", isOpen);

            if (isOpen) {
                focusedIndex = [...options].findIndex((option) =>
                    option.classList.contains("selected")
                );
                focusedIndex = focusedIndex === -1 ? 0 : focusedIndex;
                updateFocus();
            } else {
                focusedIndex = -1;
                selectButton.focus();
            }
        };

        const updateFocus = () => {
            options.forEach((option, index) => {
                if (option) {
                option.setAttribute("tabindex", index === focusedIndex ? "0" : "-1");
                    if (index === focusedIndex) option.focus();
                }
            });
        };

        const handleOptionSelect = (option) => {
            options.forEach((opt) => opt.classList.remove("selected"));
            option.classList.add("selected");
            selectedValue.textContent = option.textContent.trim(); // Update selected value
            drop_down_field.value = option.getAttribute("data-value");
            console.log(option.getAttribute("data-value"))
            console.log(drop_down_field)
            if (option.dataset.value === "clear") {
                // Reset to the default value
                selectedValue.textContent = selectedValue.getAttribute("data-default");;
                drop_down_field.value = '';
                options.forEach((opt) => opt.classList.remove("selected"));
                return;
            }
        };

        options.forEach((option) => {
            option.addEventListener("click", () => {
                handleOptionSelect(option);
                toggleDropdown(false);
            });
        });

        selectButton.addEventListener("click", () => {
            toggleDropdown();
        });

        selectButton.addEventListener("keydown", (event) => {
            if (event.key === "ArrowDown") {
                event.preventDefault();
                toggleDropdown(true);
            } else if (event.key === "Escape") {
                toggleDropdown(false);
            }
        });

        dropdown.addEventListener("keydown", (event) => {
            if (event.key === "ArrowDown") {
                event.preventDefault();
                focusedIndex = (focusedIndex + 1) % options.length;
                updateFocus();
            } else if (event.key === "ArrowUp") {
                event.preventDefault();
                focusedIndex = (focusedIndex - 1 + options.length) % options.length;
                updateFocus();
            } else if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                handleOptionSelect(options[focusedIndex]);
                toggleDropdown(false);
            } else if (event.key === "Escape") {
                toggleDropdown(false);
            }
        });

        document.addEventListener("click", (event) => {
            const isOutsideClick = !customSelect.contains(event.target);

            if (isOutsideClick) {
                toggleDropdown(false);
            }
        });
    });
});