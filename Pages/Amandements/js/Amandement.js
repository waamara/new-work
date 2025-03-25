document.addEventListener("DOMContentLoaded", () => {
    const addBtn = document.getElementById("ajouterAman");
    const modal = document.getElementById("AmandementModal");
    const closeModal = document.querySelector(".close-btn");
    const closeBtn = document.getElementById("close");
    const form = document.getElementById("AmandementForm");
    const tableBody = document.getElementById("amandementsTableBody");

    // Fields to toggle visibility
    const datePronongationField = document.getElementById("DatePronongation").closest(".form-group");
    const montantField = document.getElementById("Montant").closest(".form-group");

    // Function to update the table dynamically
    function updateTable(amendments) {
        tableBody.innerHTML = ""; // Clear existing rows

        amendments.forEach((amendment) => {
            const row = document.createElement("tr");

            row.innerHTML = `
                <td>${amendment.id}</td>
                <td>${amendment.num_amd}</td>
                <td>${amendment.date_sys}</td>
                <td>${amendment.date_prorogation || 'N/A'}</td>
                <td>${amendment.montant_amd || 'N/A'}</td>
                <td>${amendment.type_label || 'Inconnu'}</td>
                <td>
                    ${amendment.document_path 
                        ? `<a href="${amendment.document_path}" target="_blank">${amendment.nom_document || 'Voir le document'}</a>` 
                        : 'Aucun document'}
                </td>
                <td>   
                    <div>
                        <button>Modifier</button>
                    </div>
                </td>
            `;

            tableBody.appendChild(row);
        });
    }

    // Populate the table with existing amendments on page load
    if (initialAmendments && Array.isArray(initialAmendments)) {
        updateTable(initialAmendments);
    }

    // Open Modal
    addBtn.addEventListener("click", () => {
        modal.style.display = "flex";
    });

    // Close Modal
    closeModal.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Close Modal when clicking outside of it
    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    // Close Modal using the "Fermer" button
    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Toggle fields based on the selected amendment type
    const typeAmandement = document.getElementById("TypeAmandement");
    typeAmandement.addEventListener("change", () => {
        const selectedType = typeAmandement.value;

        // Reset visibility
        datePronongationField.style.display = "block";
        montantField.style.display = "block";

        // Hide fields based on the selected type
        if (selectedType == 2) { // Augmentation Montant
            datePronongationField.style.display = "none";
        } else if (selectedType == 3) { // Prolongation
            montantField.style.display = "none";
        }
    });

    // Form submission handling
    form.addEventListener("submit", (e) => {
        e.preventDefault();

        // Clear previous validation messages
        document.querySelectorAll(".validation-message").forEach((message) => {
            message.style.display = "none";
        });

        let isValid = true;

        // Validate Type Amandement
        if (!typeAmandement || !typeAmandement.value) {
            const message = document.querySelector("#TypeAmandement + .validation-message");
            if (message) message.style.display = "block";
            isValid = false;
        }

        // Validate Num Amandement
        const numAmandement = document.getElementById("NumAmandement");
        if (!numAmandement || !numAmandement.value) {
            const message = document.querySelector("#NumAmandement + .validation-message");
            if (message) message.style.display = "block";
            isValid = false;
        }

        // Validate Date Amandement
        const dateAmandement = document.getElementById("DateAmandement");
        if (!dateAmandement || !dateAmandement.value) {
            const message = document.querySelector("#DateAmandement + .validation-message");
            if (message) message.style.display = "block";
            isValid = false;
        }

        // Validate Date Pronongation (only if visible)
        const datePronongation = document.getElementById("DatePronongation");
        if (
            datePronongationField.style.display !== "none" && // Check if field is visible
            (!datePronongation || !datePronongation.value)
        ) {
            const message = document.querySelector("#DatePronongation + .validation-message");
            if (message) message.style.display = "block";
            isValid = false;
        }

        // Validate Montant (only if visible)
        const montant = document.getElementById("Montant");
        if (
            montantField.style.display !== "none" && // Check if field is visible
            (!montant || !montant.value)
        ) {
            const message = document.querySelector("#Montant + .validation-message");
            if (message) message.style.display = "block";
            isValid = false;
        }

        // Validate Document
        const documentInput = document.getElementById("Document");
        if (documentInput && documentInput.files.length === 0) {
            const message = document.querySelector("#Document + .validation-message");
            if (message) message.style.display = "block";
            isValid = false;
        } else if (
            documentInput &&
            documentInput.files[0] &&
            documentInput.files[0].type !== "application/pdf"
        ) {
            const message = document.querySelector("#Document + .validation-message");
            if (message) {
                message.textContent = "Le fichier doit être au format PDF.";
                message.style.display = "block";
            }
            isValid = false;
        }

        if (!isValid) return;

        // Prepare form data for AJAX submission
        const formData = new FormData(form);

        // Send data to the server using Fetch API
        fetch("process_form.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // Clear the form
                    form.reset();
                    modal.style.display = "none";

                    // Update the table with the new data
                    updateTable(data.data);

                    // Show success message with SweetAlert2
                    Swal.fire({
                        icon: "success",
                        title: "Succès!",
                        text: "Amendement ajouté avec succès!",
                    });
                } else {
                    // Show error message with SweetAlert2
                    Swal.fire({
                        icon: "error",
                        title: "Erreur!",
                        text: data.message,
                    });
                }
            })
            .catch((error) => {
                console.error("Error:", error);

                // Show error message with SweetAlert2
                Swal.fire({
                    icon: "error",
                    title: "Erreur!",
                    text: "Une erreur est survenue lors de l'ajout de l'amendement.",
                });
            });
    });
});