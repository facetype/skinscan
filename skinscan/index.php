<?php
require_once __DIR__ . '/includes/header.php';
?>

<script src="js/main.js"></script>

<main class="container">
    <h1>Welcome to SkinScan!</h1>
    <p>Find and track CS2 Arbitrage possibilities!</p>
    <p>Lag "Favourite items!", lagres i database</p>
    <p><a href="about.php">Go to About Page</a></p>

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


</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
