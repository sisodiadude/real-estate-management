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
        { name: "salary", label: "Salary", required: true, type: "number", min: 0 },
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
    ],
    step6: [
        { name: "resume", label: "Resume/CV", required: true, type: "file", accept: ".pdf,image/*" },
        { name: "profile_picture", label: "Profile Picture", required: true, type: "file", accept: "image/*" },
        { name: "govt_id", label: "Government ID", required: true, type: "file", accept: ".pdf,image/*", multiple: true },
        { name: "education_certificates", label: "Education Certificates", required: true, type: "file", accept: ".pdf,image/*", multiple: true }
    ]
};

function navigateStep(current, next) {
    // console.log(`Navigating from step ${current} to step ${next}`);

    // Update step container classes
    // console.log(`Removing "active" from step-${current} and adding "disabled"`);
    document.querySelector(`.step-container.step-${current}`).classList.remove("active");
    document.querySelector(`.step-container.step-${current}`).classList.add("disabled");

    // console.log(`Adding "active" to step-${next}`);
    document.querySelector(`.step-container.step-${next}`).classList.add("active");

    // Toggle visibility of step elements
    // console.log(`Showing step-${next} and hiding step-${current}`);
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

    // console.log(`Step transition from ${current} to ${next} completed.`);
}

/**
 * Validates form inputs for a specific wizard step based on predefined rules.
 * @param {number} stepNumber - The step number to validate (e.g., 1, 2, 3).
 * @returns {boolean} - True if the step is valid, false otherwise.
 */
function validateFormStep(stepNumber) {
    console.log(`➡️ Validating Step ${stepNumber}...`);

    // Ensure the form exists
    const form = document.getElementById("branchForm");
    if (!form) {
        console.error("❌ Form element with ID 'branchForm' not found.");
        return false;
    }

    // Get step-specific inputs
    const stepInputs = formInputs[`step${stepNumber}`];
    if (!stepInputs || !Array.isArray(stepInputs)) {
        console.error(`❌ No valid inputs defined for step ${stepNumber}.`);
        return false;
    }

    let isValid = true;
    let firstErrorField = null;

    // Iterate over each input in the step
    stepInputs.forEach(input => {
        const field = form.querySelector(`[name="${input.name}"]`);
        if (!field) {
            console.warn(`⚠️ Field '${input.label}' not found in DOM for step ${stepNumber}.`);
            return;
        }

        // Ensure feedback element exists
        let feedback = field.parentElement.querySelector(".invalid-feedback");
        if (!feedback) {
            feedback = document.createElement("div");
            feedback.className = "invalid-feedback";
            field.parentElement.appendChild(feedback);
        }

        const value = field.type === "checkbox" ? field.checked : field.value.trim();
        let fieldIsValid = true;

        // Validation rules
        if (input.required && !value && (input.type !== "checkbox" || !field.checked)) {
            feedback.textContent = `${input.label} is required.`;
            fieldIsValid = false;
        } else if (value) { // Only validate further if there's a value
            switch (input.type) {
                case "text":
                case "email":
                    if (input.minLength && value.length < input.minLength) {
                        feedback.textContent = `${input.label} must be at least ${input.minLength} characters.`;
                        fieldIsValid = false;
                    } else if (input.maxLength && value.length > input.maxLength) {
                        feedback.textContent = `${input.label} must not exceed ${input.maxLength} characters.`;
                        fieldIsValid = false;
                    } else if (input.regex && !input.regex.test(value)) {
                        feedback.textContent = input.type === "email"
                            ? "Please enter a valid email address."
                            : `${input.label} must be in a valid format.`;
                        fieldIsValid = false;
                    }
                    break;

                case "number":
                    const numValue = parseFloat(value);
                    if (isNaN(numValue)) {
                        feedback.textContent = `${input.label} must be a valid number.`;
                        fieldIsValid = false;
                    } else if (input.min !== undefined && numValue < input.min) {
                        feedback.textContent = `${input.label} must be at least ${input.min}.`;
                        fieldIsValid = false;
                    } else if (input.max !== undefined && numValue > input.max) {
                        feedback.textContent = `${input.label} must not exceed ${input.max}.`;
                        fieldIsValid = false;
                    } else if (input.step && (numValue % input.step !== 0)) {
                        feedback.textContent = `${input.label} must be in increments of ${input.step}.`;
                        fieldIsValid = false;
                    }
                    break;

                case "date":
                    const dateValue = new Date(value);
                    const minDate = input.min ? new Date(input.min) : null;
                    const maxDate = input.max ? new Date(input.max) : null;

                    if (isNaN(dateValue.getTime())) {
                        feedback.textContent = `Please enter a valid date for ${input.label}.`;
                        fieldIsValid = false;
                    } else if (minDate && dateValue < minDate) {
                        feedback.textContent = `${input.label} cannot be earlier than ${input.min}.`;
                        fieldIsValid = false;
                    } else if (maxDate && dateValue > maxDate) {
                        feedback.textContent = input.name === "date_of_birth"
                            ? "You must be at least 18 years old."
                            : `${input.label} cannot be later than ${input.max}.`;
                        fieldIsValid = false;
                    }
                    break;

                case "select":
                    if (!input.externallyManaged && input.options && !input.options.includes(value)) {
                        feedback.textContent = `Please select a valid option for ${input.label}.`;
                        fieldIsValid = false;
                    }
                    break;

                case "checkbox":
                    // No additional validation needed beyond required check
                    break;

                case "file":
                    const files = field.files;
                    if (input.required && (!files || files.length === 0)) {
                        feedback.textContent = `${input.label} is required.`;
                        fieldIsValid = false;
                    } else if (files && files.length > 0) {
                        const accept = input.accept.split(",").map(type => type.trim());
                        for (let file of files) {
                            const fileType = file.type || file.name.split('.').pop().toLowerCase();
                            if (!accept.includes(fileType) && !accept.some(type => fileType.match(type.replace("*", ".*")))) {
                                feedback.textContent = `${input.label} must be of type: ${input.accept}.`;
                                fieldIsValid = false;
                                break;
                            }
                        }
                        if (input.multiple && files.length > 1 && !input.multiple) {
                            feedback.textContent = `${input.label} allows only one file.`;
                            fieldIsValid = false;
                        }
                    }
                    break;

                case "array":
                    // Handle array inputs (allowances, deductions)
                    const arrayFields = form.querySelectorAll(`[name^="${input.name}["]`);
                    arrayFields.forEach(arrayField => {
                        const arrayValue = arrayField.value.trim();
                        if (arrayValue) {
                            const nameParts = arrayField.name.match(/\[(\d+)\]\[(type|amount)\]/);
                            if (nameParts && nameParts[2] === "type" && !input.options.includes(arrayValue)) {
                                feedback.textContent = `Invalid ${input.label} type selected.`;
                                fieldIsValid = false;
                            } else if (nameParts && nameParts[2] === "amount") {
                                const amount = parseFloat(arrayValue);
                                if (isNaN(amount) || amount < 0) {
                                    feedback.textContent = `${input.label} amount must be a positive number.`;
                                    fieldIsValid = false;
                                }
                            }
                        }
                    });
                    break;

                default:
                    console.warn(`⚠️ Unsupported input type '${input.type}' for ${input.label}.`);
            }
        }

        // Update field styling and track overall validity
        if (!fieldIsValid) {
            field.classList.add("is-invalid");
            if (!firstErrorField) firstErrorField = field;
            isValid = false;
        } else {
            field.classList.remove("is-invalid");
            feedback.textContent = "";
        }
    });

    // Finalize validation
    if (!isValid) {
        console.warn(`❌ Validation failed for step ${stepNumber}.`);
        form.classList.add("was-validated");
        if (firstErrorField) {
            firstErrorField.focus();
            firstErrorField.scrollIntoView({ behavior: "smooth", block: "center" });
        }
    } else {
        console.log(`✅ Step ${stepNumber} validated successfully.`);
        form.classList.remove("was-validated");
    }

    return isValid;
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
            // console.log('Current data-step (Next):', stepNumber);
            if (validateFormStep(stepNumber)) {
                // console.log("Form validation successful. Proceeding to step:", stepNumber + 1);
                // alert("Form validation successful. Proceeding to next step...");
                navigateStep(stepNumber, stepNumber + 1);
            } else {
                console.warn("Form validation failed on step:", stepNumber);
            }
        }

        // Check if the clicked element is a previous-button
        if (target.matches('.previous-button')) {
            // console.log('Current data-step (Previous):', stepNumber);
            // console.log("Navigating to previous step:", stepNumber - 1);
            // alert("Navigating to previous step...");
            navigateStep(stepNumber, stepNumber - 1);
        }
    });
});
