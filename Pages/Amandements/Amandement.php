<?php
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

// Retrieve garantie_id from the URL
$garantieId = isset($_GET['garantie_id']) ? intval($_GET['garantie_id']) : null;

if ($garantieId === null) {
    echo "Votre Garantie ID is missing.";
    exit;
}

// Fetch details of the garantie from the database
$sql = "SELECT * FROM garantie WHERE id = ? ORDER BY garantie.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$garantieId]);
$garantie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$garantie) {
    echo "Garantie not found."; 
    exit;
}

// Fetch all existing amendments for the current garantie_id
$sql = "SELECT a.*, t.label AS type_label 
        FROM amandement a 
        LEFT JOIN Type_amd t ON a.type_amd_id = t.id 
        WHERE a.garantie_id = ? ORDER BY a.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$garantieId]);
$amendments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all amendment types from the Type_amd table
$sqlType_AMD = "SELECT id, label FROM Type_amd";
$stmt = $pdo->prepare($sqlType_AMD);
$stmt->execute();
$typeAmandements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="css/Amandements.css">
<script defer src="js/Amandement.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<main>
    <h2>Liste des Amandements de la Garantie n°<?= htmlspecialchars($garantie['num_garantie']) ?></h2>
    <button id="ajouterAman">Ajouter Amandement</button>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Num Amandement</th>
                    <th>Date Amandement</th>
                    <th>Date Pronongation</th>
                    <th>Montant</th>
                    <th>Observation</th>
                    <th>Type Amandement</th>
                    <th>Document</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="amandementsTableBody">
                <!-- Data will be dynamically added here -->
            </tbody>
        </table>
    </div>

    <div class="modal" id="AmandementModal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2 id="modalTitle">Ajouter Amandement </h2>
            <form id="AmandementForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="garantie_id" value="<?= htmlspecialchars($garantieId) ?>">
                <div class="form-group">
                    <label>Type Amandement</label>
                    <select id="TypeAmandement" name="type_amandement" class="form-control">
                        <option value="" disabled selected>Sélectionnez un type</option>
                        <?php foreach ($typeAmandements as $type): ?>
                            <option value="<?= htmlspecialchars($type['id']) ?>">
                                <?= htmlspecialchars($type['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="validation-message">Vous devez sélectionner un type d'amandement.</span>
                </div>
                <div class="form-group">
                    <label>Num Amandement</label>
                    <input type="text" id="NumAmandement" name="Num_amandement">
                    <span class="validation-message">Vous n'avez pas rempli ce champ.</span>
                </div>
                <div class="form-group">
                    <label>Date Amandement</label>
                    <input type="date" id="DateAmandement" name="date_amandement">
                    <span class="validation-message">Vous n'avez pas rempli ce champ.</span>
                </div>
                <div class="form-group">
                    <label>Date Pronongation</label>
                    <input type="date" id="DatePronongation" name="date_pronongation">
                    <span class="validation-message">Vous n'avez pas rempli ce champ.</span>
                </div>
                <div class="form-group">
                    <label>Document</label>
                    <input type="file" id="Document" name="document" accept=".pdf">
                    <span class="validation-message">Vous devez sélectionner un fichier PDF.</span>
                </div>
                <div class="form-group">
                    <label>Montant</label>
                    <input type="number" id="Montant" name="montant">
                    <span class="validation-message">Vous n'avez pas rempli ce champ.</span>
                </div>
                <div class="form-group">
                    <label>Observation</label>
                    <textarea id="Observation" name="observation"></textarea>
                    <span class="validation-message">Vous n'avez pas rempli ce champ.</span>
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <button type="button" class="btn btn-secondary" id="close">Fermer</button>
                <div class="error-message" id="formError"></div>
            </form>
        </div>
    </div>
</main>

<!-- Pass the amendments data to JavaScript -->
<script>
    const initialAmendments = <?= json_encode($amendments) ?>;
</script>

<?php
require_once('../Template/footer.php');
?>