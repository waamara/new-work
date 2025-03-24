<?php
require_once("../../db_connection/db_conn.php");

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $garantieId = isset($_POST['garantie_id']) ? intval($_POST['garantie_id']) : 0;
    $typeAmandement = isset($_POST['type_amandement']) ? intval($_POST['type_amandement']) : 0;
    $dateAmandement = isset($_POST['date_amandement']) ? trim($_POST['date_amandement']) : '';
    $datePronongation = isset($_POST['date_pronongation']) ? trim($_POST['date_pronongation']) : '';
    $montant = isset($_POST['montant']) ? floatval($_POST['montant']) : 0;
    $observation = isset($_POST['observation']) ? trim($_POST['observation']) : '';

    // Handle file upload
    $filePath = null; // Default value if no file is uploaded
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['document']['tmp_name'];
        $fileName = basename($_FILES['document']['name']); // Sanitize the filename
        $filePath = 'uploads/' . $fileName;

        // Ensure the uploads directory exists
        if (!is_dir('uploads/')) {
            mkdir('uploads/', 0777, true);
        }

        // Move the uploaded file to the desired directory
        if (!move_uploaded_file($fileTmpPath, $filePath)) {
            die(json_encode(['success' => false, 'message' => 'Error uploading file.']));
        }
    }

    // Insert data into the database
    try {
        $sql = "INSERT INTO amandement (
                    num_amd, date_sys, date_prorogation, montant_amd, garantie_id, type_amd_id, document_path, observation
                ) VALUES (
                    :num_amd, :date_sys, :date_prorogation, :montant_amd, :garantie_id, :type_amd_id, :document_path, :observation
                )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':num_amd' => uniqid('AMD_', true), // Generate a unique ID for the amendment
            ':date_sys' => $dateAmandement,
            ':date_prorogation' => $datePronongation,
            ':montant_amd' => $montant,
            ':garantie_id' => $garantieId,
            ':type_amd_id' => $typeAmandement, // Store the selected type_amd_id
            ':document_path' => $filePath, // Path to the uploaded file
            ':observation' => $observation,
        ]);

        // Fetch updated list of amendments for the current garantie_id
        $sql = "SELECT a.*, t.label AS type_label 
                FROM amandement a 
                LEFT JOIN Type_amd t ON a.type_amd_id = t.id 
                WHERE a.garantie_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$garantieId]);
        $amendments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $amendments]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'insertion des données: ' . $e->getMessage()]);
    }
}
?>