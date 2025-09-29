<?php
require_once __DIR__ . '/../src/services/csgo_empire_client.php';
require_once __DIR__ . '/../src/services/cs_float_client.php';
require_once __DIR__ . '/includes/header.php';

$empireResult = null;
$csfloatResult = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['test_empire']) || isset($_POST['test_both'])) {
        try {
            $empireClient = new EmpireClient();
            $empireResult = $empireClient->GetPriceList();
        } catch (Exception $e) {
            $empireResult = ["error" => $e->getMessage()];
        }
    }

    if (isset($_POST['test_csfloat']) || isset($_POST['test_both'])) {
        try {
            $csfloatClient = new CSFloatClient();
            $csfloatResult = $csfloatClient->GetPriceList();
        } catch (Exception $e) {
            $csfloatResult = ["error" => $e->getMessage()];
        }
    }
}

function countItems($result) {
    if (isset($result['error'])) {
        return "Error";
    }
    if (is_array($result)) {
        return count($result);
    }
    return 0;
}
?>

<main class="container">
    <h1>Welcome to SkinScan 🚀</h1>
    <p>Find and track CS2 Arbitrage possibilities!</p>
    <p><a href="about.php">Go to About Page</a></p>

    <h2>API Test</h2>
    <form method="POST">
        <button type="submit" name="test_empire">Test Empire API</button>
        <button type="submit" name="test_csfloat">Test CSFloat API</button>
        <button type="submit" name="test_both">Test Both APIs</button>
    </form>

    <?php if ($empireResult): ?>
        <h3>Empire API Result (<?php echo countItems($empireResult); ?> items)</h3>
        <textarea rows="15" cols="80"><?php echo htmlspecialchars(json_encode($empireResult, JSON_PRETTY_PRINT)); ?></textarea>
    <?php endif; ?>

    <?php if ($csfloatResult): ?>
        <h3>CSFloat API Result (<?php echo countItems($csfloatResult); ?> items)</h3>
        <textarea rows="15" cols="80"><?php echo htmlspecialchars(json_encode($csfloatResult, JSON_PRETTY_PRINT)); ?></textarea>
    <?php endif; ?>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
