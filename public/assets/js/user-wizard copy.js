(function ($) {
    "use strict";
    const formInputs = {
        step1: [
            { name: "first_name", label: "First name", required: true, type: "text", minLength: 3, maxLength: 50 },
            { name: "last_name", label: "Last name", required: true, type: "text", minLength: 3, maxLength: 50 },
            { name: "email", label: "Email", required: true, type: "email", minLength: 5, maxLength: 100, regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
            { name: "alternative_email", label: "Alternative email", required: false, type: "email", minLength: 5, maxLength: 100, regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
            { name: "mobile", label: "Mobile", required: true, type: "text", minLength: 10, maxLength: 15, regex: /^[0-9]+$/ },
            { name: "alternate_mobile", label: "Alternative mobile", required: false, type: "text", minLength: 10, maxLength: 15, regex: /^[0-9]+$/ },
            {
                name: "date_of_birth",
                label: "Date of birth",
                required: true,
                type: "date",
                min: "1900-01-01",
                max: new Date(new Date().setFullYear(new Date().getFullYear() - 18)).toISOString().split('T')[0]
            },
            { name: "marital_status", label: "Marital status", required: false, type: "select", options: ["single", "married", "divorced", "widowed"] },
            { name: "nationality_id", label: "Nationality", required: true, type: "select", externallyManaged: true },
            { name: "blood_group", label: "Blood group", required: false, type: "select", options: ["A+", "A-", "B+", "B-", "O+", "O-", "AB+", "AB-"] },
            { name: "account_status", label: "Account status", required: true, type: "select", options: ["active", "inactive", "suspended", "archived"] }
        ],
        step2: [
            { name: "current_address_line1", label: "Current Address Line 1", required: true, type: "text", maxLength: 100 },
            { name: "current_address_line2", label: "Current Address Line 2", required: false, type: "text", maxLength: 100 },
            { name: "current_country_id", label: "Current Country", required: true, type: "select", externallyManaged: true },
            { name: "current_state_id", label: "Current State", required: true, type: "select", externallyManaged: true },
            { name: "current_city_id", label: "Current City", required: true, type: "select", externallyManaged: true },
            { name: "current_postal_code", label: "Current Postal Code", required: true, type: "text", maxLength: 10 },
            { name: "same_as_current_address", label: "Same as Current Address", required: false, type: "checkbox" },
            { name: "permanent_address_line1", label: "Permanent Address Line 1", required: true, type: "text", maxLength: 100 },
            { name: "permanent_address_line2", label: "Permanent Address Line 2", required: false, type: "text", maxLength: 100 },
            { name: "permanent_country_id", label: "Permanent Country", required: true, type: "select", externallyManaged: true },
            { name: "permanent_state_id", label: "Permanent State", required: true, type: "select", externallyManaged: true },
            { name: "permanent_city_id", label: "Permanent City", required: true, type: "select", externallyManaged: true },
            { name: "permanent_postal_code", label: "Permanent Postal Code", required: true, type: "text", maxLength: 10 }
        ]
    };

    function navigateStep(current, next) {
        document.querySelector(`.step-container.step-${current}`).classList.remove("active");
        document.querySelector(`.step-container.step-${current}`).classList.add("disabled");
        document.querySelector(`.step-container.step-${next}`).classList.add("active");

        document.querySelector(`.wizard-step-${next}`).classList.add("d-block");
        document.querySelector(`.wizard-step-${next}`).classList.remove("d-none");
        document.querySelector(`.wizard-step-${current}`).classList.remove("d-block");
        document.querySelector(`.wizard-step-${current}`).classList.add("d-none");
    }

    function validateFormStep(stepNumber) {
        console.log(`➡️ Validating Step ${stepNumber}...`);

        const form = document.getElementById("branchForm");
        if (!form) {
            console.error("❌ Form element not found");
            return false;
        }

        let firstErrorField = null;
        let formIsValid = true;

        const stepInputs = formInputs[`step${stepNumber}`];
        if (!stepInputs) {
            console.error(`❌ No inputs found for step ${stepNumber}`);
            return false;
        }

        stepInputs.forEach(input => {
            const field = document.querySelector(`[name="${input.name}"]`);
            if (!field) return console.warn(`⚠️ '${input.label}' not found in the DOM`);

            let feedback = field.parentElement.querySelector(".invalid-feedback");
            if (!feedback) {
                feedback = document.createElement("div");
                feedback.className = "invalid-feedback";
                field.parentElement.appendChild(feedback);
            }

            let isValid = true;
            const value = field.value.trim();

            // Required validation
            if (input.required && !value) {
                feedback.textContent = `${input.label} is required.`;
                isValid = false;
            }

            // Min/Max length validation
            if (value && (input.type === "text" || input.type === "email")) {
                if (input.minLength && value.length < input.minLength) {
                    feedback.textContent = `${input.label} should have at least ${input.minLength} characters.`;
                    isValid = false;
                }
                if (input.maxLength && value.length > input.maxLength) {
                    feedback.textContent = `${input.label} should not exceed ${input.maxLength} characters.`;
                    isValid = false;
                }
            }

            // Regex validation
            if (input.regex && value && !input.regex.test(value)) {
                feedback.textContent = `Please enter a valid ${input.label}.`;
                isValid = false;
            }

            // Date validation
            if (input.type === "date" && value) {
                const enteredDate = new Date(value);
                const minDate = new Date(input.min);
                const maxDate = new Date(input.max);

                if (enteredDate < minDate) {
                    feedback.textContent = `The date for ${input.label} cannot be earlier than ${input.min}.`;
                    isValid = false;
                }
                if (enteredDate > maxDate) {
                    feedback.textContent = `You must be at least 18 years old to proceed.`;
                    isValid = false;
                }
            }

            // Select field validation
            if (input.type === "select" && input.required && !input.externallyManaged && input.options && !input.options.includes(value)) {
                feedback.textContent = `Please select a valid option for ${input.label}.`;
                isValid = false;
            }

            // Apply styles
            if (!isValid) {
                field.classList.add("is-invalid");
                if (!firstErrorField) firstErrorField = field;
                formIsValid = false;
            } else {
                field.classList.remove("is-invalid");
                feedback.textContent = "";
            }
        });

        // Focus first error field if any
        if (firstErrorField) {
            console.warn(`📌 First invalid field: ${firstErrorField.name}`);
            form.classList.add("was-validated");
            firstErrorField.focus();
            firstErrorField.scrollIntoView({ behavior: "smooth", block: "center" });
            return false;
        }

        form.classList.remove("was-validated");
        console.log(`✅ Step ${stepNumber} validation successful.`);
        return true;
    }

    /*=====================
        Wizard JS
    ==========================*/
    document.addEventListener("click", function (event) {
        if (event.target.matches(".next-button")) {
            console.log("Next button clicked.");

            let currentStep = event.target.closest(".wizard-step");
            if (!currentStep) {
                console.error("Error: .wizard-step not found for Next button.");
                return;
            }

            let stepNumber = parseInt(currentStep.dataset.step);
            console.log("Current step:", stepNumber);

            if (validateFormStep(stepNumber)) {
                console.log("Form validation successful. Proceeding to step:", stepNumber + 1);
                alert("Form validation successful. Proceeding...");
                navigateStep(stepNumber, stepNumber + 1);
            } else {
                console.warn("Form validation failed on step:", stepNumber);
            }
        }

        if (event.target.matches(".previous-button")) {
            console.log("Previous button clicked.");

            let currentStep = event.target.closest(".wizard-step");
            if (!currentStep) {
                console.error("Error: .wizard-step not found for Previous button.");
                return;
            }

            let stepNumber = parseInt(currentStep.dataset.step);
            console.log("Current step:", stepNumber);

            alert("Going back to the previous step...");
            navigateStep(stepNumber, stepNumber - 1);
        }
    });



})(jQuery);
