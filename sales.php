
<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_sale'])) {
    $product_id = intval($_POST['product_id']);
    $qty_to_sell = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];

    // Retrieve active details to establish safe transactional checking operations
    $prod_check = $conn->query("SELECT price, stock_quantity FROM Products WHERE product_id = $product_id");
    if ($prod_check && $prod_check->num_rows === 1) {
        $pdata = $prod_check->fetch_assoc();
        $current_stock = $pdata['stock_quantity'];
        $unit_price = $pdata['price'];

        if ($qty_to_sell > $current_stock) {
            $message = "<div class='bg-rose-100 border-l-4 border-rose-500 text-rose-700 p-4 mb-4 rounded-xl text-sm'>Error: Insufficient stock. Current available quantity is $current_stock items.</div>";
        } else {
            $total_cost = $unit_price * $qty_to_sell;

            // Initialize database entry records inside master tables cleanly
            $conn->query("INSERT INTO Sales (user_id, total_amount) VALUES ($user_id, $total_cost)");
            $sale_id = $conn->insert_id;

            // Generate relational child entry row records mapped across data models
            $conn->query("INSERT INTO Sales_Items (sale_id, product_id, quantity, unit_price) VALUES ($sale_id, $product_id, $qty_to_sell, $unit_price)");

            // Auto-reduce product stock quantity
            $conn->query("UPDATE Products SET stock_quantity = stock_quantity - $qty_to_sell WHERE product_id = $product_id");

            $message = "<div class='bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-4 rounded-xl text-sm'>Sale processed successfully. Transaction complete.</div>";
        }
    }
}

$products_list = $conn->query("SELECT product_id, name, price, stock_quantity FROM Products WHERE stock_quantity > 0");
?>

<div class="space-y-6 max-w-4xl">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Sales Counter Terminal</h1>
        <p class="text-sm text-slate-400">Process real-time outbounds and decrement stock levels instantly.</p>
    </div>

    <?= $message ?>

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1">Select Item Variant</label>
                <select name="product_id" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-900 dark:text-white">
                    <?php while ($p = $products_list->fetch_assoc()): ?>
                        <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?> — (Price: Ksh <?= $p['price'] ?> | Available Stock: <?= $p['stock_quantity'] ?>)</option>
                    <?php endwhile; ?> </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1">Quantity Requested</label>
                <input type="number" name="quantity" min="1" value="1" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-900 dark:text-white">
            </div>
            <button type="submit" name="process_sale" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-all shadow-lg shadow-emerald-600/20">Finalize Sale Invoice</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>