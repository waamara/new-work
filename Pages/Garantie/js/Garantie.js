// Add this script at the end of your page, before the closing </body> tag
document.addEventListener('DOMContentLoaded', function() {
    // Get all column toggle buttons
    const toggleButtons = document.querySelectorAll('.column-toggle');
    // Add click event listeners
    console.log("Garantie.js has been loaded successfully!");
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            toggleButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get the column group to show
            const columnGroup = this.dataset.column;
            
            // Get all table columns
            const table = document.querySelector('table');
            const headerCells = table.querySelectorAll('th');
            const rows = table.querySelectorAll('tbody tr');
            
            // Show/hide columns based on the selected group
            if (columnGroup === 'all') {
                // Show all columns
                headerCells.forEach(cell => cell.style.display = '');
                rows.forEach(row => {
                    Array.from(row.cells).forEach(cell => cell.style.display = '');
                });
            } 
            else if (columnGroup === 'essential') {
                // Show only essential columns (adjust indices as needed)
                const essentialColumns = [0, 1, 5, 7, 9, 11, 13, 15, 16]; // ID, Num, Montant, Direction, Fournisseur, Monnaie, Agence, Appel d'Offre, Actions
                
                headerCells.forEach((cell, index) => {
                    cell.style.display = essentialColumns.includes(index) ? '' : 'none';
                });
                
                rows.forEach(row => {
                    Array.from(row.cells).forEach((cell, index) => {
                        cell.style.display = essentialColumns.includes(index) ? '' : 'none';
                    });
                });
            }
            else if (columnGroup === 'details') {
                // Show only detail columns (adjust indices as needed)
                const detailColumns = [0, 1, 2, 3, 4, 5, 16]; // ID, Num, Dates, Montant, Actions
                
                headerCells.forEach((cell, index) => {
                    cell.style.display = detailColumns.includes(index) ? '' : 'none';
                });
                
                rows.forEach(row => {
                    Array.from(row.cells).forEach((cell, index) => {
                        cell.style.display = detailColumns.includes(index) ? '' : 'none';
                    });
                });
            }
        });
    });
});