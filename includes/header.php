<?php
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

// Fetch live low stock indicators dynamically (< 5 units)
$low_stock_query = "SELECT name, stock_quantity FROM Products WHERE stock_quantity < 5";
$low_stock_res = $conn->query($low_stock_query);
$alerts = [];
while ($row = $low_stock_res->fetch_assoc()) {
    $alerts[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StockCore Inventory Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-200">
    <div class="flex h-screen overflow-hidden">