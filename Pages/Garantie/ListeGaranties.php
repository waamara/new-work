<?php
$page_css = "garantie.css"; 
$page_js = "garantie.js"; 
require_once("../Template/header.php");
require_once("../../db_connection/db_conn.php");

$sql = "SELECT 
g.id AS garantie_id, g.num_garantie, g.date_creation, g.date_emission, g.date_validite, 
g.montant, 
d.id AS direction_id, d.libelle AS direction, 
f.id AS fournisseur_id, f.nom_fournisseur, 
m.id AS monnaie_id, m.label AS monnaie, 
a.id AS agence_id, a.label AS agence, 
ao.id AS appel_offre_id, ao.num_appel_offre 
FROM garantie g
JOIN direction d ON g.direction_id = d.id
JOIN fournisseur f ON g.fournisseur_id = f.id
JOIN monnaie m ON g.monnaie_id = m.id
JOIN agence a ON g.agence_id = a.id
JOIN appel_offre ao ON g.appel_offre_id = ao.id";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$garanties = $stmt->fetchAll();
?> 



<!-- <link rel="stylesheet" href="../Garantie/css/garantie.css"> -->

<!-- Merged Dashboard Content -->
<main>
    <div>
        <h2>Liste des Garanties</h2>
        <button class="ajoutergar">Ajouter garantie</button>
        <table>
            <thead>
                <tr>
                    <th>ID Garantie</th>
                    <th>Numéro Garantie</th>
                    <th>Date de Création</th>
                    <th>Date d'Émission</th>
                    <th>Date de Validité</th>
                    <th>Montant</th>
                    <th>ID Direction</th>
                    <th>Direction</th>
                    <th>ID Fournisseur</th>
                    <th>Fournisseur</th>
                    <th>ID Monnaie</th>
                    <th>Monnaie</th>
                    <th>ID Agence</th>
                    <th>Agence</th>
                    <th>ID Appel d'Offre</th>
                    <th>Numéro Appel d'Offre</th>
                    <th>Modifier</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($garanties)) : ?>
                    <?php foreach ($garanties as $row) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['garantie_id']) ?></td>
                            <td><?= htmlspecialchars($row['num_garantie']) ?></td>
                            <td><?= htmlspecialchars($row['date_creation']) ?></td>
                            <td><?= htmlspecialchars($row['date_emission']) ?></td>
                            <td><?= htmlspecialchars($row['date_validite']) ?></td>
                            <td><?= number_format($row['montant'], 2) ?> DA</td>
                            <td><?= htmlspecialchars($row['direction_id']) ?></td>
                            <td><?= htmlspecialchars($row['direction']) ?></td>
                            <td><?= htmlspecialchars($row['fournisseur_id']) ?></td>
                            <td><?= htmlspecialchars($row['nom_fournisseur']) ?></td>
                            <td><?= htmlspecialchars($row['monnaie_id']) ?></td>
                            <td><?= htmlspecialchars($row['monnaie']) ?></td>
                            <td><?= htmlspecialchars($row['agence_id']) ?></td>
                            <td><?= htmlspecialchars($row['agence']) ?></td>
                            <td><?= htmlspecialchars($row['appel_offre_id']) ?></td>
                            <td><?= htmlspecialchars($row['num_appel_offre']) ?></td>
                            <td>
                                <form method="POST" action="update_garantie.php">
                                    <input type="text" name="garantie_id" value="<?= $row['garantie_id'] ?>">
                                    <button type="submit">Modifier</button>
                                </form>

                                <form method="POST" action="../Authentification/authentification.php">
                                    <input type="text" name="garantie_id" value="<?= $row['garantie_id'] ?>">
                                    <button type="submit" >Authentification</button>
                                </form>

                                <form method="POST" action="../Amandements/Amandement.php">
                                    <input type="text" name="garantie_id" value="<?= $row['garantie_id'] ?>">
                                    <button type="submit"><a href="../Amandements/Amandement.php?garantie_id=<?= htmlspecialchars($row['garantie_id']) ?>" class="btn btn-primary">Amendement</a></button>
                                </form>

                                <form method="POST" action="../Liberation/liberation.php">
                                    <input type="text" name="garantie_id" value="<?= $row['garantie_id'] ?>">
                                    <button type="submit">Libération</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="17">Aucune garantie trouvée.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
</main>

<?php
require_once('../Template/footer.php');

?>
