<?php
echo "<h3>Current Project Directory:</h3>" . __DIR__ . "<br><br>";

echo "<h3>Checking for 'includes' folder:</h3>";
if (is_dir(__DIR__ . '/includes')) {
    echo "'includes' folder exists!<br>";
    
    echo "<h3>Checking files inside 'includes':</h3>";
    echo file_exists(__DIR__ . '/includes/header.php') ? "header.php found!<br>" : "❌ header.php is MISSING!<br>";
    echo file_exists(__DIR__ . '/includes/sidebar.php') ? "sidebar.php found!<br>" : "❌ sidebar.php is MISSING!<br>";
} else {
    echo "❌ 'includes' folder does not exist or is misspelled! Current items here:<br>";
    print_r(scandir(__DIR__));
}
?>