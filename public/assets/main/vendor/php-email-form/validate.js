(function () {
    "use strict";

    const forms = document.querySelectorAll(".php-email-form");

    forms.forEach((form) => {
        form.addEventListener("submit", async (event) => {
            event.preventDefault();

            const action = form.getAttribute("action");
            if (!action) {
                displayError(form, "Form action is missing.");
                return;
            }

            toggleState(form, "loading");

            try {
                const response = await fetch(action, {
                    method: "POST",
                    body: new FormData(form),
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                });

                const data = await response.json();

                toggleState(form, "idle");

                if (!response.ok) {
                    throw data;
                }

                form.querySelector(".sent-message").classList.add("d-block");
                form.reset();
            } catch (error) {
                let message = "Something went wrong. Please try again.";

                if (error?.errors) {
                    message = Object.values(error.errors).flat().join("<br>");
                } else if (error?.message) {
                    message = error.message;
                }

                displayError(form, message);
            }
        });
    });

    function toggleState(form, state) {
        form.querySelector(".loading").classList.toggle(
            "d-block",
            state === "loading",
        );
        form.querySelector(".error-message").classList.remove("d-block");
        form.querySelector(".sent-message").classList.remove("d-block");
    }

    function displayError(form, message) {
        form.querySelector(".loading").classList.remove("d-block");
        const errorBox = form.querySelector(".error-message");
        errorBox.innerHTML = message;
        errorBox.classList.add("d-block");
    }
})();
