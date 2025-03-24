<?php
 require_once("../Template/header.php");



 if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["garantie_id"])) {
    $garantie_id =htmlspecialchars( $_POST["garantie_id"]); // Récupérer l'ID de la garantie

    //recuperation des infrmation de la autheitinifcat de la garanite 
    

    
} else {
    die("Requête invalide !");
}
?>

<h1>Liberation page id de la garanntie <?php isset($garantie_id) ? $garantie_id : null; ?>  </h1>

    <!-- Merged Dashboard Content -->
    <main id="dynamic-content">

      
    </main>
    <?php
 require_once("../Template/footer.php");

?>