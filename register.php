<?php
session_start();

$usersFile = "users.txt";
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $password2 = $_POST["password2"];

    if ($username === "" || $password === "" || $password2 === "") {
        $error = "Vyplňte prosím všechna pole.";
    } elseif ($password !== $password2) {
        $error = "Hesla se neshodují.";
    } else {
        $users = [];
        if (file_exists($usersFile)) {
            $lines = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                [$user, $hash] = explode("|", $line);
                $users[$user] = $hash;
            }
        }

        if (isset($users[$username])) {
            $error = "Uživatel s tímto jménem již existuje.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            file_put_contents($usersFile, "$username|$hash\n", FILE_APPEND);
            $success = "Registrace proběhla úspěšně. Nyní se můžete přihlásit.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <title>Registrace</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
        }

        .form-control {
            border-radius: 2rem;
        }

        .btn-primary {
            border-radius: 2rem;
        }

        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="card p-4">
    <h3 class="mb-4 text-center">Registrace</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Jméno</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Zadejte uživatelské jméno" required>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label">Heslo</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Zadejte heslo" required>
        </div>
        <div class="mb-4">
            <label for="password2" class="form-label">Ověřit heslo</label>
            <input type="password" class="form-control" id="password2" name="password2" placeholder="Zadejte heslo znovu" required>
        </div>
        <div class="d-grid mb-4">
            <button type="submit" class="btn btn-primary" id="register">Registrovat se</button>
        </div>
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Již máte účet? Přihlaste se!</a>
        </div>
    </form>
</div>
</body>
</html>