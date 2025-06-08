<?php
if (!isset($_SESSION)) session_start();
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    textarea.form-control {
        border-radius: 1rem;
        resize: vertical;
    }

    .header {
        color: white;
        text-align: center;
        margin-bottom: 30px;
    }

    .custom-navbar {
        background: linear-gradient(135deg, #667eea, #764ba2);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    body {
        background: linear-gradient(135deg, #667eea, #764ba2);
        min-height: 100vh;
    }

    .container {
        max-width: 800px;
        margin-top: 100px;
    }

    .card {
        border-radius: 1rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }

    .post-box {
        border-radius: 1rem;
        background-color: #fff;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .my-post {
        background-color: #f0f8ff;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<nav class="navbar navbar-expand-lg navbar-dark custom-navbar fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand">Fórum bez pravidel</span>
        <div class="d-flex ms-auto">
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo htmlspecialchars($_SESSION["username"]); ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="profile.php">Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="?logout=1">Odhlásit se</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>