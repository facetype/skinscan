<?php
session_start();
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../src/db/connection.php';

// Handle login if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Sanitize input
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {

            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                // Store user info in session
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error = 'An error occurred. Please try again later.';
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    session_destroy();
    header('Location: index.php');
    exit;
}
?>


<script src="js/main.js"></script>

<main class="container">
    <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>
        <!-- LOGIN FORM -->
        <h1>Login to SkinScan</h1>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <button type="submit" name="login">Login</button>
        </form>

    <?php else: ?>
        <!-- LOGGED-IN VIEW -->
        <h1>Welcome to SkinScan, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        <p>Find and track CS2 Arbitrage possibilities!</p>
        <p>Lag "Favourite items!" (lagres i database)</p>
        <p><a href="about.php">Go to About Page</a></p>
        <p><a href="?logout=1">Log out</a></p>

        <h2>Arbitrage Scanner</h2>
        <button id="checkArb">Check Arbitrage</button>
        <p id="status" style="margin-top: 10px; font-weight: bold;"></p>

        <table id="arbResults" class="arb-table">
            <thead>
                <tr>
                    <th data-sort="market_hash_name">Item Name <span class="arrow"></span></th>
                    <th data-sort="wear_name">Wear <span class="arrow"></span></th>
                    <th data-sort="direction">Direction <span class="arrow"></span></th>
                    <th data-sort="empire_price">Empire Price <span class="arrow"></span></th>
                    <th data-sort="float_price">Float Price <span class="arrow"></span></th>
                    <th data-sort="profit">Profit ($) <span class="arrow"></span></th>
                    <th data-sort="profit_percent">Profit % <span class="arrow"></span></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <p id="status"></p>
    <?php endif; ?>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
