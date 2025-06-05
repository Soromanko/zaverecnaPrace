<?php
session_start();

$postsFile = "posts.txt";
$adminUsername = "Soromanko";

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET["delete"]) && $_SESSION["username"] === $adminUsername) {
    $deleteId = $_GET["delete"];
    if (file_exists($postsFile)) {
        $lines = file($postsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $newLines = [];
        foreach ($lines as $line) {
            if (!str_starts_with($line, "$deleteId|")) {
                $newLines[] = $line;
            }
        }
        file_put_contents($postsFile, implode("\n", $newLines) . "\n");
    }
    header("Location: forum.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["post"]) && !empty($_POST["subject"])) {
    $predmet = str_replace("\n", " ", trim($_POST["subject"]));
    $text = str_replace("\n", "|n|", trim($_POST["post"]));
    $autor = $_SESSION["username"];
    $cas = date("d.m.Y H:i");
    $id = uniqid();
    $entry = "$id|$cas|$autor|$predmet|$text\n";
    file_put_contents($postsFile, $entry, FILE_APPEND);
    header("Location: forum.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Fórum</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin-top: 60px;
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

        textarea.form-control {
            border-radius: 1rem;
            resize: vertical;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .header {
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="container position-relative">
    <a href="?logout=true" class="btn btn-outline-light logout-btn">Odhlásit se</a>

    <h2 class="header">Jste přihlášen jako uživatel <?= htmlspecialchars($_SESSION["username"]) ?></h2>

    <div class="card p-4 mb-4">
        <form method="post">
            <div class="mb-3">
                <label for="subject" class="form-label">Předmět</label>
                <input type="text" class="form-control" id="subject" name="subject" placeholder="Napiš předmět příspěvku" required>
            </div>
            <div class="mb-3">
                <label for="post" class="form-label">Napiš nový příspěvek</label>
                <textarea name="post" id="post" rows="4" class="form-control" placeholder="Napiš svůj příspěvek..." required></textarea>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Odeslat</button>
            </div>
        </form>
    </div>

    <div class="card p-4">
        <h5 class="mb-3">Příspěvky:</h5>
        <?php
if (file_exists($postsFile)) {
    $lines = file($postsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach (array_reverse($lines) as $line) {
        [$id, $cas, $autor, $predmet, $text] = explode("|", trim($line), 5);
        $safeSubject = htmlspecialchars($predmet);
        $safeText = nl2br(htmlspecialchars(str_replace("|n|", "\n", $text)));
        $isMyPost = $autor === $_SESSION["username"];
        $highlight = $isMyPost ? 'bg-light' : 'bg-white';

        echo "<div class='p-4 mb-4 border rounded shadow-sm $highlight'>";
        echo "<h5 class='fw-bold mb-2'>$safeSubject</h5>";
        echo "<div class='mb-3'>$safeText</div>";
        echo "<div class='d-flex justify-content-between text-muted small'>";
        echo "<span>$autor &bull; $cas</span>";
        if ($_SESSION["username"] === $adminUsername) {
            echo "<a href='?delete=$id' class='text-danger text-decoration-none' onclick=\"return confirm('Opravdu chcete smazat tento příspěvek?');\">Smazat</a>";
        }
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p>Žádné příspěvky.</p>";
}

        ?>
    </div>
</div>

</body>
</html>
