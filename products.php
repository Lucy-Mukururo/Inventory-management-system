<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// BLOCK STAFF: Redirect them to sales.php if they try to access the product catalog page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "<script>window.location.href='sales.php';</script>";
    exit();
}

$action_msg = '';
//normal code
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/header.php';
// ... rest of your code ...

require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$action_msg = '';

// Check if form data is posted and process additions safely if user is Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    if ($_SESSION['role'] !== 'Admin') {
        $action_msg = "<div class='bg-rose-100 border-l-4 border-rose-500 text-rose-700 p-4 mb-4 rounded-xl text-sm'>Access Denied: Only Admin accounts can add items.</div>";
    } else {
        $name = $conn->real_escape_string($_POST['name']);
        $category = $conn->real_escape_string($_POST['category']);
        $sku = $conn->real_escape_string($_POST['sku']);
        $price = floatval($_POST['price']);
        $qty = intval($_POST['quantity']);
        $supplier_id = !empty($_POST['supplier_id']) ? intval($_POST['supplier_id']) : 'NULL';

        $insert_q = "INSERT INTO Products (name, category, sku, price, stock_quantity, supplier_id) 
                     VALUES ('$name', '$category', '$sku', $price, $qty, $supplier_id)";
        
        if ($conn->query($insert_q)) {
            $action_msg = "<div class='bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-4 rounded-xl text-sm'>Product added successfully to inventory.</div>";
        } else {
            $action_msg = "<div class='bg-rose-100 border-l-4 border-rose-500 text-rose-700 p-4 mb-4 rounded-xl text-sm'>Error handling catalog insertion code execution.</div>";
        }
    }
}

// Fetch lists of records to build structured view data
$products = $conn->query("SELECT p.*, s.supplier_name FROM Products p LEFT JOIN Suppliers s ON p.supplier_id = s.supplier_id ORDER BY p.product_id DESC");
$suppliers = $conn->query("SELECT supplier_id, supplier_name FROM Suppliers");
?>

<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Inventory Catalog</h1>
            <p class="text-sm text-slate-400">Manage available products and configurations.</p>
        </div>
    </div>

    <?= $action_msg ?>

    <?php if ($_SESSION['role'] === 'Admin'): ?>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <h3 class="text-sm font-bold uppercase text-slate-400 mb-4">Add New Inventory Line Item (Admin Authorized Only)</h3>
        <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="name" placeholder="Product Title (e.g. Sony WH-1000XM4)" required class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <select name="category" required class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="Laptop">Laptop</option>
                <option value="Headphones">Headphones</option>
                <option value="Flashdisk">Flashdisk</option>
                <option value="Powerbank">Powerbank</option>
                <option value="Smartwatch">Smartwatch</option>
            </select>
            <input type="text" name="sku" placeholder="SKU Serial Code Bar" required class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <input type="number" step="0.01" name="price" placeholder="Selling Price (Ksh)" required class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <input type="number" name="quantity" placeholder="Starting Quantity Count" required class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <select name="supplier_id" class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Select Associated Supplier Record</option>
                <?php while($sup = $suppliers->fetch_assoc()): ?>
                    <option value="<?= $sup['supplier_id'] ?>"><?= htmlspecialchars($sup['supplier_name']) ?></option>
                <?php endwhile; ?> </select>
            <div class="md:col-span-3">
                <button type="submit" name="add_product" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2 rounded-xl transition-all shadow-md">Commit Entry to Database</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800 text-xs font-bold uppercase text-slate-400 border-b border-slate-200 dark:border-slate-700">
                    <th class="p-4">SKU Code</th>
                    <th class="p-4">Item Identity Designation</th>
                    <th class="p-4">Category Variant</th>
                    <th class="p-4">Unit Pricing Matrix</th>
                    <th class="p-4">Remaining Count Status</th>
                    <th class="p-4">Assigned Supplier Origin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                <?php while ($p = $products->fetch_assoc()): 
                    $is_low = $p['stock_quantity'] < 5;
                ?>
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                        <td class="p-4 font-mono text-xs font-bold text-slate-500"><?= htmlspecialchars($p['sku']) ?></td>
                        <td class="p-4 font-semibold"><?= htmlspecialchars($p['name']) ?></td>
                        <td class="p-4"><span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-xs font-medium"><?= $p['category'] ?></span></td>
                        <td class="p-4 font-medium">Ksh <?= number_format($p['price'], 2) ?></td>
                        <td class="p-4">
                            <?php if ($is_low): ?>
                                <span class="px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 font-bold text-xs"><i class="fa-solid fa-arrow-trend-down mr-1"></i> Low Alert: <?= $p['stock_quantity'] ?></span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 font-medium text-xs">Healthy: <?= $p['stock_quantity'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-slate-400"><?= htmlspecialchars($p['supplier_name'] ?? 'N/A Unassigned') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>