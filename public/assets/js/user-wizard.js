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
    ],
    step3: [
        { name: "designation", label: "Designation", required: true, type: "text", maxLength: 100 },
        {
            name: "joining_date",
            label: "Joining Date",
            required: true,
            type: "date",
            min: "1900-01-01",
            max: new Date(new Date().setMonth(new Date().getMonth() + 2)).toISOString().split('T')[0]
        },
        { name: "probation_period", label: "Probation Period (Months)", required: false, type: "number", min: 0, max: 24 },
        { name: "employment_type", label: "Employment Type", required: true, type: "select", options: ["full_time", "part_time", "contract", "internship"] }
    ],
    step4: [
        { name: "salary", label: "Salary", required: true, type: "number", min: 0, step: 0.01 },
        { name: "bank_account", label: "Bank Account Number", required: true, type: "text" },
        { name: "bank_name", label: "Bank Name", required: true, type: "text" },
        {
            name: "allowances", label: "Allowances", required: false, type: "array", options: [
                "house_rent", "dearness", "travel", "medical", "conveyance", "performance_bonus", "overtime", "food", "education", "special", "entertainment", "communication", "internet", "shift", "leave_travel", "uniform", "child_education"
            ]
        },
        {
            name: "deductions", label: "Deductions", required: false, type: "array", options: [
                "tax", "insurance", "retirement", "loan", "other"
            ]
        },
        { name: "ifsc_swift_code", label: "IFSC/SWIFT Code", required: true, type: "text", regex: /^[A-Z0-9]+$/i },
        { name: "pan_tax_id", label: "PAN or Tax", required: true, type: "text", regex: /^[A-Z0-9]+$/i },
        { name: "salary_frequency", label: "Salary Payment Frequency", required: true, type: "select", options: ["weekly", "biweekly", "semimonthly", "monthly", "quarterly", "semiannually", "annually"] },
    ],
    step5: [
        { name: "emergency_contact_name", label: "Name", required: false, type: "text", minLength: 3, maxLength: 50 },
        { name: "emergency_contact_relation", label: "Relationship", required: false, type: "text", minLength: 3, maxLength: 50 },
        { name: "emergency_contact_number", label: "Contact Number", required: false, type: "text", minLength: 10, maxLength: 15, regex: /^[0-9]+$/ },
    ]
};

function navigateStep(current, next) {
    console.log(`Navigating from step ${current} to step ${next}`);

    // Update step container classes
    console.log(`Removing "active" from step-${current} and adding "disabled"`);
    document.querySelector(`.step-container.step-${current}`).classList.remove("active");
    document.querySelector(`.step-container.step-${current}`).classList.add("disabled");

    console.log(`Adding "active" to step-${next}`);
    document.querySelector(`.step-container.step-${next}`).classList.add("active");

    // Toggle visibility of step elements
    console.log(`Showing step-${next} and hiding step-${current}`);
    const nextStepElement = document.querySelector(`.wizard-container[data-step="${next}"]`);
    const currentStepElement = document.querySelector(`.wizard-container[data-step="${current}"]`);

    if (nextStepElement) {
        nextStepElement.classList.add("d-block");
        nextStepElement.classList.remove("d-none");
    } else {
        console.warn(`Element for step-${next} not found!`);
    }

    if (currentStepElement) {
        currentStepElement.classList.remove("d-block");
        currentStepElement.classList.add("d-none");
    } else {
        console.warn(`Element for step-${current} not found!`);
    }

    console.log(`Step transition from ${current} to ${next} completed.`);
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
            switch (input.name) {
                case 'ifsc_swift_code':
                case 'pan_tax_id':
                    feedback.textContent = `${input.label} must be alphanumeric.`;
                    break;

                default:
                    feedback.textContent = `Please enter a valid ${input.label}.`;
                    break;
            }
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
                switch (input.name) {
                    case 'date_of_birth':
                        feedback.textContent = `You must be at least 18 years old to proceed.`;
                        break;

                    case 'joining_date':
                        feedback.textContent = `The joining date cannot be later than 2 months from the current date.`;
                        break;

                    default:
                        feedback.textContent = `The date for ${input.label} cannot be later than ${input.min}.`;
                        break;
                }
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
// Step 1 validation

document.querySelectorAll('.wizard-container .next-button, .wizard-container .previous-button').forEach(button => {
    button.addEventListener('click', function (event) {
        const target = event.target;
        const wizardContainer = target.closest('.wizard-container'); // Get the specific wizard container
        const stepNumber = parseInt(wizardContainer.getAttribute('data-step'), 10);

        // Check if the clicked element is a next-button
        if (target.matches('.next-button')) {
            console.log('Current data-step (Next):', stepNumber);
            if (validateFormStep(stepNumber)) {
                console.log("Form validation successful. Proceeding to step:", stepNumber + 1);
                alert("Form validation successful. Proceeding to next step...");
                navigateStep(stepNumber, stepNumber + 1);
            } else {
                console.warn("Form validation failed on step:", stepNumber);
            }
        }

        // Check if the clicked element is a previous-button
        if (target.matches('.previous-button')) {
            console.log('Current data-step (Previous):', stepNumber);
            console.log("Navigating to previous step:", stepNumber - 1);
            alert("Navigating to previous step...");
            navigateStep(stepNumber, stepNumber - 1);
        }
    });
});
