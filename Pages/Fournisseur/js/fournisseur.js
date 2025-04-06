/**
 * Function to dynamically update the amendments table
 * @param {Array} amendments - List of amendments to display in the table
 */
function updateTable(amendments) {
    const tableBody = document.getElementById("amendementsTableBody");
    tableBody.innerHTML = ""; // Clear existing rows

    amendments.forEach((amendment) => {
        const row = document.createElement("tr");

        // Add cells for each column
        row.innerHTML = `
            <td>${amendment.id}</td>
            <td>${amendment.num_amd}</td>
            <td>${amendment.date_sys}</td>
            <td>${amendment.date_prorogation || "N/A"}</td>
            <td>${amendment.montant_amd || "N/A"}</td>
            <td>${amendment.type_label || "Inconnu"}</td>
            <td>
                ${amendment.nom_document 
                    ? `<a href="${amendment.document_path}" target="_blank">${amendment.nom_document}</a>` 
                    : "Aucun document"}
            </td>
            <td>
                <button class="modify-btn" data-id="${amendment.id}">Modifier</button>
            </td>
        `;

        tableBody.appendChild(row);
    });
}

// Initialize the table with initialAmendments passed from PHP
if (initialAmendments && Array.isArray(initialAmendments)) {
    updateTable(initialAmendments);
}

/**
 * Handle Form Submission (Add or Modify Amendment)
 */
const form = document.getElementById("AmandementForm");
form.addEventListener("submit", (e) => {
    e.preventDefault();

    // Clear previous validation messages
    clearValidationMessages();

    // Gather form data
    const formData = new FormData(form);
    const garantieId = formData.get("garantie_id");
    const typeAmandement = formData.get("type_amd_id");
    const numAmandement = formData.get("num_amd");
    const dateAmandement = formData.get("date_sys");
    const datePronongation = formData.get("date_prorogation");
    const montantAmandement = formData.get("montant_amd");
    const amendmentId = formData.get("amendment_id"); // For modification
    const file = formData.get("document");

    // Validate required fields
    let isValid = true;

    if (!typeAmandement) {
        document.querySelector("#TypeAmandement + .validation-message").style.display = "block";
        isValid = false;
    }
    if (!numAmandement) {
        document.querySelector("#NumAmandement + .validation-message").style.display = "block";
        isValid = false;
    }
    if (!dateAmandement) {
        document.querySelector("#DateAmandement + .validation-message").style.display = "block";
        isValid = false;
    }

    // Additional validation for specific types
    if (typeAmandement == 2 && !montantAmandement) {
        document.querySelector("#Montant + .validation-message").style.display = "block";
        isValid = false;
    }
    if (typeAmandement == 3 && !datePronongation) {
        document.querySelector("#DatePronongation + .validation-message").style.display = "block";
        isValid = false;
    }

    if (!isValid) {
        return;
    }

    // Determine the endpoint and method
    const url = amendmentId
        ? `update_amendment.php`
        : `process_form.php`;
    const method = amendmentId ? "POST" : "POST";

    // Send the form data via AJAX
    fetch(url, {
        method: method,
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                Swal.fire({
                    icon: "success",
                    title: "Succès!",
                    text: amendmentId
                        ? "L'amendement a été modifié avec succès."
                        : "L'amendement a été ajouté avec succès.",
                });

                // Update the table with the new data
                updateTable(data.data);

                // Close the modal
                closeModal();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Erreur!",
                    text: data.message,
                });
            }
        })
        .catch((error) => {
            console.error("Error submitting form:", error);
            Swal.fire({
                icon: "error",
                title: "Erreur!",
                text: "Une erreur est survenue lors de la soumission du formulaire.",
            });
        });
});

/**
 * Handle Modify Button Clicks
 */
const tableBody = document.getElementById("amendementsTableBody");
tableBody.addEventListener("click", (event) => {
    if (event.target.classList.contains("modify-btn")) {
        const amendmentId = event.target.dataset.id;

        // Fetch amendment details from the backend
        fetch(`get_amendment.php?id=${amendmentId}`)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    const amendment = data.data;

                    // Populate the form fields
                    document.getElementById("NumAmandement").value = amendment.num_amd;
                    document.getElementById("DateAmandement").value = amendment.date_sys;
                    document.getElementById("TypeAmandement").value = amendment.type_amd_id || "";
                    document.getElementById("DatePronongation").value = amendment.date_prorogation || "";
                    document.getElementById("Montant").value = amendment.montant_amd || "";

                    // Handle optional document field
                    const documentField = document.getElementById("Document");
                    documentField.value = ""; // Reset file input

                    // Add hidden input for amendment ID if it doesn't exist
                    let hiddenInput = document.querySelector('input[name="amendment_id"]');
                    if (!hiddenInput) {
                        hiddenInput = document.createElement("input");
                        hiddenInput.type = "hidden";
                        hiddenInput.name = "amendment_id";
                        document.getElementById("AmandementForm").appendChild(hiddenInput);
                    }
                    hiddenInput.value = amendmentId;

                    // Update modal title and show the modal
                    document.querySelector("#modalTitle").textContent = "Modifier Amendement";
                    document.getElementById("AmandementModal").style.display = "flex";
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Erreur!",
                        text: data.message,
                    });
                }
            })
            .catch((error) => {
                console.error("Error fetching amendment details:", error);
                Swal.fire({
                    icon: "error",
                    title: "Erreur!",
                    text: "Une erreur est survenue lors de la récupération des détails de l'amendement.",
                });
            });
    }
});

/**
 * Toggle fields based on the selected amendment type
 */
const typeAmandement = document.getElementById("TypeAmandement");
const datePronongationField = document.querySelector("#DatePronongation").parentElement;
const montantField = document.querySelector("#Montant").parentElement;

typeAmandement.addEventListener("change", () => {
    const selectedType = typeAmandement.value;

    // Reset visibility
    datePronongationField.style.display = "block";
    montantField.style.display = "block";

    // Hide fields based on the selected type
    if (selectedType == 2) {
        // Augmentation Montant
        datePronongationField.style.display = "none";
    } else if (selectedType == 3) {
        // Prolongation
        montantField.style.display = "none";
    }
});

/**
 * Clear all validation error messages
 */
function clearValidationMessages() {
    document.querySelectorAll(".validation-message").forEach((message) => {
        message.style.display = "none";
    });
}

/**
 * Close the modal
 */
function closeModal() {
    document.getElementById("AmandementModal").style.display = "none";
    document.getElementById("AmandementForm").reset();
    clearValidationMessages();
}

// Close modal when clicking the close button or outside the modal
document.querySelector(".close-btn").addEventListener("click", closeModal);
document.getElementById("close").addEventListener("click", closeModal);
window.addEventListener("click", (event) => {
    const modal = document.getElementById("AmandementModal");
    if (event.target === modal) {
        closeModal();
    }
});