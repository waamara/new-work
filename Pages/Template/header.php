
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css">
     <!-- CSS du header (style global) -->
    <link rel="stylesheet" href="css/style.css">

    <!-- CSS spécifique à chaque page -->
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="css/<?php echo $page_css; ?>">
    <?php endif; ?>

    <title>PFE</title>
</head>
<body>
   <!-- SIDEBAR -->
   <?php require_once("../Template/sidebar.php") ;?>

<!-- MAIN CONTENT -->
<section id="content">
    <nav>
        <i class='bx bx-menu toggle-sidebar'></i>
        <form action="#">
            <div class="form-group">
                <input type="text" placeholder="Search...">
                <i class='bx bx-search icon'></i>
            </div>
        </form>
        <a href="#" class="nav-link">
            <i class='bx bxs-bell icon'></i>
            <span class="badge">5</span>
        </a>
        <a href="#" class="nav-link">
            <i class='bx bxs-message-square-dots icon'></i>
            <span class="badge">8</span>
        </a>
        <span class="divider"></span>
        <div class="profile">
            <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?ixid=MnwxMjA3fDB8MHxzZWFyY2h8NHx8cGVvcGxlfGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="">
        </div>
    </nav>