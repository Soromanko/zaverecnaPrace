<?php
if (!isset($_SESSION)) session_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">Jednoduché fórum</span>
        <div class="d-flex ms-auto text-white">
            <span class="me-3">Přihlášen jako: <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong></span>
            <a href="forum.php?logout=1" class="btn btn-outline-light btn-sm">Odhlásit</a>
        </div>
    </div>
</nav>
