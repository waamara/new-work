<?php
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

// Récupérer l'ID de la garantie depuis la requête POST
if (isset($_POST['garantie_id'])) {
    $garantie_id = $_POST['garantie_id'];

    // Requête SQL pour récupérer les détails de la garantie
    $sql = "SELECT 
                g.id AS garantie_id, g.num_garantie, g.date_creation, g.date_emission, g.date_validite, 
                g.montant, 
                d.libelle AS direction, 
                f.nom_fournisseur, 
                m.label AS monnaie, 
                a.label AS agence, 
                ao.num_appel_offre 
            FROM garantie g
            JOIN direction d ON g.direction_id = d.id
            JOIN fournisseur f ON g.fournisseur_id = f.id
            JOIN monnaie m ON g.monnaie_id = m.id
            JOIN agence a ON g.agence_id = a.id
            JOIN appel_offre ao ON g.appel_offre_id = ao.id
            WHERE g.id = :garantie_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':garantie_id', $garantie_id, PDO::PARAM_INT);
    $stmt->execute();
    $garantie = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    echo "ID de garantie non spécifié.";
    exit;
}
?>

<!-- Lien vers le fichier CSS spécifique -->
<link rel="stylesheet" href="../Authentification/css/authentificaton.css">

<!-- Contenu Principal -->
<main>
    <div class="auth-container">
        <h2 class="auth-title">Authentification de la Garantie</h2>

        <!-- Informations de la Garantie -->
        <?php if ($garantie) : ?>
            <div class="garantie-info">
                <p><strong>ID Garantie:</strong> <?= htmlspecialchars($garantie['garantie_id']) ?></p>
                <p><strong>Numéro Garantie:</strong> <?= htmlspecialchars($garantie['num_garantie']) ?></p>
                <p><strong>Date de Création:</strong> <?= htmlspecialchars($garantie['date_creation']) ?></p>
                <p><strong>Date d'Émission:</strong> <?= htmlspecialchars($garantie['date_emission']) ?></p>
                <p><strong>Date de Validité:</strong> <?= htmlspecialchars($garantie['date_validite']) ?></p>
                <p><strong>Montant:</strong> <?= number_format($garantie['montant'], 2) ?> DA</p>
                <p><strong>Direction:</strong> <?= htmlspecialchars($garantie['direction']) ?></p>
                <p><strong>Fournisseur:</strong> <?= htmlspecialchars($garantie['nom_fournisseur']) ?></p>
                <p><strong>Monnaie:</strong> <?= htmlspecialchars($garantie['monnaie']) ?></p>
                <p><strong>Agence:</strong> <?= htmlspecialchars($garantie['agence']) ?></p>
                <p><strong>Numéro Appel d'Offre:</strong> <?= htmlspecialchars($garantie['num_appel_offre']) ?></p>
            </div>
        <?php else : ?>
            <p>Aucune garantie trouvée avec cet ID.</p>
        <?php endif; ?>

        <!-- Formulaire d'Authentification -->
        <form method="POST" action="../../Backend/authentifier_garantie.php">
            <input type="hidden" name="garantie_id" value="<?= htmlspecialchars($garantie['garantie_id']) ?>">

            <label for="auth_code">Code d'Authentification:</label>
            <input type="text" id="auth_code" name="auth_code" required>

            <label for="auth_date">Date d'Authentification:</label>
            <input type="date" id="auth_date" name="auth_date" required>

            <label for="auth_notes">Notes (facultatif):</label>
            <textarea id="auth_notes" name="auth_notes"></textarea>

            <button type="submit">Soumettre l'Authentification</button>
        </form>
    </div>
</main>

<?php
require_once("../Template/footer.php");
?>