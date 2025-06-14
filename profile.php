<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$uploadsDir = "uploads/";
if (!file_exists($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}

$defaultImage = "default.png";
$defaultImagePath = $uploadsDir . $defaultImage;
if (!file_exists($defaultImagePath)) {
    copy("images/" . $defaultImage, $defaultImagePath);
}

$usersFile = "users.txt";
$defaultImage = "default.png";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        $allowedTypes = ["image/jpeg", "image/png", "image/gif"];
        $maxSize = 5 * 1024 * 1024;

        if (in_array($_FILES["profile_picture"]["type"], $allowedTypes) &&
            $_FILES["profile_picture"]["size"] <= $maxSize) {

            $fileExtension = pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
            $newFileName = $_SESSION["username"] . "_" . time() . "." . $fileExtension;
            $targetPath = $uploadsDir . $newFileName;

            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetPath)) {
                $users = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $newUsers = [];

                foreach ($users as $user) {
                    $userData = explode("|", $user);
                    if ($userData[0] === $_SESSION["username"]) {
                        $userData[2] = $newFileName;
                        $user = implode("|", $userData);
                    }
                    $newUsers[] = $user;
                }

                file_put_contents($usersFile, implode("\n", $newUsers) . "\n");
                $message = "Profile picture updated successfully!";
            } else {
                $message = "Error uploading file.";
            }
        } else {
            $message = "Invalid file type or size too large (max 5MB).";
        }
    }
}

$profilovka = $defaultImage;
if (file_exists($usersFile)) {
    $users = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($users as $user) {
        $userData = explode("|", $user);
        if ($userData[0] === $_SESSION["username"] && !empty($userData[2])) {
            $profilovka = $userData[2];
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin-top: 60px;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h2 class="text-center mb-4">Nastavení profilu</h2>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="text-center mb-4">
            <img src="<?= htmlspecialchars($uploadsDir . $profilovka) ?>"
                 alt="Profile Picture"
                 class="profile-picture mb-3">
            <h4><?= htmlspecialchars($_SESSION["username"]) ?></h4>
        </div>

        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Změna profilovky</label>
                <input type="file"
                       class="form-control"
                       id="profile_picture"
                       name="profile_picture"
                       accept="image/jpeg,image/png,image/gif">
                <div class="form-text">Maximální velikost souboru: 5MB. Povolené soubory: JPG, PNG, GIF</div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Uložit</button>
                <a href="forum.php" class="btn btn-outline-secondary">Zpět</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>