<?php
session_start();
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: /~270445/skinscan/login.html");
    exit;
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../src/db/connection.php';
?>


<main class="container">
    <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>
        <!-- REACT -->
        <h1>Login to SkinScan</h1>
        <div id="login-root"></div>
        <script type="module" src="js/loginApp.js"></script>

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


        <div id="filters" style="margin: 20px 0;">
            <label>
                <input type="checkbox" id="excludeStattrak">
                Exclude StatTrak
            </label>

            <label style="margin-left: 20px;">
                Min Value ($):
                <input type="number" id="minValue" placeholder="0" min="0">
            </label>

            <label style="margin-left: 20px;">
                Max Value ($):
                <input type="number" id="maxValue" placeholder="No limit" min="0">
            </label>

            <button id="applyFilters" style="margin-left: 20px;">Apply Filters</button>
        </div>





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

<script src="js/main.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>