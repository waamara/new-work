<?php
// Database connection details
$host = 'localhost';
$dbname = 'db_sonatrach_dp';
$username = 'root';
$password = '';

try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get raw POST data
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    if (!$data || !array_key_exists('id', $data) || 
        !array_key_exists('code_fournisseur', $data) || 
        !array_key_exists('nom_fournisseur', $data) || 
        !array_key_exists('raison_sociale', $data) || 
        !array_key_exists('pays_id', $data)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    $id = $data['id'];
    $codeFournisseur = $data['code_fournisseur'];
    $nomFournisseur = $data['nom_fournisseur'];
    $raisonSociale = $data['raison_sociale'];
    $paysId = $data['pays_id'];

    // Update query
    $stmt = $pdo->prepare("UPDATE fournisseur 
                            SET  code_fournisseur = :code_fournisseur, 
                                nom_fournisseur = :nom_fournisseur, 
                                raison_sociale = :raison_sociale, 
                                pays_id = :pays_id 
                            WHERE id = :id");
    $stmt->execute([
        'id' => $id,
        'code_fournisseur' => $codeFournisseur,
        'nom_fournisseur' => $nomFournisseur,
        'raison_sociale' => $raisonSociale,
        'pays_id' => $paysId
    ]);

    // Return success response
    echo json_encode(['status' => 'success', 'message' => 'Fournisseur modifié avec succès']);
} catch (Exception $e) {
    // Return error response
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>