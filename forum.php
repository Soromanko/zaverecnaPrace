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
</head>
<body>
<?php require_once ("header.php"); ?>

<div class="container">
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

            $userAvatars = [];
            $usersFile = "users.txt";
            $uploadsDir = "uploads/";
            $defaultImage = "default.png";

            if (file_exists($usersFile)) {
                $users = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($users as $userLine) {
                    $userData = explode("|", $userLine);
                    if (count($userData) >= 3) {
                        $userAvatars[$userData[0]] = $userData[2] ?: $defaultImage;
                    }
                }
            }

            foreach (array_reverse($lines) as $line) {
                [$id, $cas, $autor, $predmet, $text] = explode("|", trim($line), 5);
                $safeSubject = htmlspecialchars($predmet);
                $safeText = nl2br(htmlspecialchars(str_replace("|n|", "\n", $text)));
                $isMyPost = $autor === $_SESSION["username"];
                $highlight = $isMyPost ? 'bg-light' : 'bg-white';

                $avatarFile = isset($userAvatars[$autor]) ? $userAvatars[$autor] : $defaultImage;
                $profileImagePath = $uploadsDir . $avatarFile;
                if (!file_exists($profileImagePath) || !is_file($profileImagePath)) {
                    $profileImagePath = $uploadsDir . $defaultImage;
                }

                echo "<div class='p-4 mb-4 border rounded shadow-sm $highlight'>";
                echo "<h5 class='fw-bold mb-2'>$safeSubject</h5>";
                echo "<div class='mb-3'>$safeText</div>";
                echo "<div class='d-flex align-items-center justify-content-between text-muted small'>";
                echo "<div class='d-flex align-items-center'>";
                echo "<img src='" . htmlspecialchars($profileImagePath) . "' alt='avatar' style='width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px;'>";
                echo "<span><strong>" . htmlspecialchars($autor) . "</strong> &bull; $cas</span>";
                echo "</div>";
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

<?php require_once ("footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
