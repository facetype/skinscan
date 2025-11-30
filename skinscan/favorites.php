<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Favorites - SkinScan</title>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

<style>
    .remove-fav {
        cursor: pointer;
        color: #ff4d4d;
        font-size: 18px;
        font-weight: bold;
    }
</style>

</head>
<body class="bg-dark text-light">

<div class="container mt-4">

    <h1 class="mb-3">Your Favorite Items</h1>

    <div class="mb-3">
        <a href="index.php" class="btn btn-secondary btn-sm">⬅ Back to Scanner</a>
        <a href="?logout=1" class="btn btn-danger btn-sm ml-2">Log Out</a>
    </div>

    <p id="status" class="font-weight-bold"></p>

    <div class="table-responsive mt-4">
        <table id="favTable" class="table table-striped table-dark table-hover table-sm">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Wear</th>
                    <th>Direction</th>
                    <th>Empire Price</th>
                    <th>Float Price</th>
                    <th>Profit ($)</th>
                    <th>Profit %</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<script src="js/favorites.js"></script>

</body>
</html>
