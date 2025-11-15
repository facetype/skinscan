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

<main class="container my-5">

    <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>

        <!-- LOGIN VIEW (React) -->
        <h1 class="mb-4">Login to SkinScan</h1>
        <div id="login-root"></div>
        <script type="module" src="js/loginApp.js"></script>

    <?php else: ?>

        <!-- LOGGED-IN VIEW -->
        <div class="mb-4">
            <h1 class="mb-3">Welcome to SkinScan, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
            <p>Find and track CS2 arbitrage opportunities!</p>
            <p>Create “Favourite Items” (stored in database)</p>

            <div class="mt-3">
                <a href="about.php" class="btn btn-info btn-sm mr-2">About</a>
                <a href="?logout=1" class="btn btn-danger btn-sm">Log Out</a>
            </div>
        </div>

        <!-- SCANNER -->
        <h2 class="mb-3">Arbitrage Scanner</h2>

        <button id="checkArb" class="btn btn-primary mb-3">Check Arbitrage</button>
        <p id="status" class="font-weight-bold"></p>

        <!-- FILTERS (DARK BOOTSTRAP STYLE) -->
        <div id="filters" class="p-3 mb-4 rounded" style="background:#212529;border:1px solid #343a40;">
            <div class="row g-3 align-items-end">

                <div class="col-md-2">
                    <div class="form-check">
                        <input type="checkbox" id="excludeStattrak" class="form-check-input filter-input">
                        <label class="form-check-label text-light" for="excludeStattrak">Exclude StatTrak</label>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-light" for="minValue">Min Value ($)</label>
                    <input type="number" id="minValue" class="form-control form-control-sm filter-input" placeholder="0"
                        min="0">
                </div>

                <div class="col-md-2">
                    <label class="form-label text-light" for="maxValue">Max Value ($)</label>
                    <input type="number" id="maxValue" class="form-control form-control-sm filter-input"
                        placeholder="No limit" min="0">
                </div>

                <div class="col-md-2">
                    <label class="form-label text-light" for="maxProfitPercentage">Max Profit (%)</label>
                    <input type="number" id="maxProfitPercentage" class="form-control form-control-sm filter-input"
                        placeholder="100" min="0">
                </div>

                <div class="col-md-2">
                    <button id="applyFilters" class="btn btn-secondary btn-sm w-100">Apply Filters</button>
                </div>

            </div>
        </div>

        <!-- RESULTS TABLE -->
        <div class="table-responsive mt-4">
            <table id="arbResults" class="table table-striped table-dark table-hover table-sm">
                <thead>
                    <tr>
                        <th scope="col" data-sort="market_hash_name">Item Name</th>
                        <th scope="col" data-sort="wear_name">Wear</th>
                        <th scope="col" data-sort="direction">Direction</th>
                        <th scope="col" data-sort="empire_price">Empire Price</th>
                        <th scope="col" data-sort="float_price">Float Price</th>
                        <th scope="col" data-sort="profit">Profit ($)</th>
                        <th scope="col" data-sort="profit_percent">Profit %</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    <?php endif; ?>

</main>

<script src="js/main.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>