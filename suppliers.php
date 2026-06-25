<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// BLOCK STAFF: Redirect them to sales.php if they try to access the product catalog page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "<script>window.location.href='sales.php';</script>";
    exit();
}

$action_msg = '';

// Force error reporting so if anything goes wrong, it prints a message instead of a blank screen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// 1. Handle adding a new supplier (If your system supports it)
$action_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_supplier'])) {
    if ($_SESSION['role'] !== 'Admin') {
        $action_msg = "<div class='bg-rose-100 border-l-4 border-rose-500 text-rose-700 p-4 mb-4 rounded-xl text-sm'>Access Denied: Only Admin accounts can add suppliers.</div>";
    } else {
        $sup_name = $conn->real_escape_string($_POST['supplier_name']);
        $contact = $conn->real_escape_string($_POST['contact_info'] ?? '');

        $insert_q = "INSERT INTO Suppliers (supplier_name, contact_info) VALUES ('$sup_name', '$contact')";
        if ($conn->query($insert_q)) {
            $action_msg = "<div class='bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-4 rounded-xl text-sm'>Supplier registered successfully.</div>";
        } else {
            $action_msg = "<div class='bg-rose-100 border-l-4 border-rose-500 text-rose-700 p-4 mb-4 rounded-xl text-sm'>Error executing supplier database entry.</div>";
        }
    }
}

/* 2. Fetch Supplier Delivery Data
 NOTE: If your database uses a specific "Deliveries" or "Stock_Incoming" table, change the table names below.
 This default query joins Products directly with Suppliers to show current stock assignments.
*/
$query = "SELECT 
            s.supplier_name, 
            p.name AS product_name, 
            p.stock_quantity, 
            p.created_at AS delivery_date 
          FROM Products p 
          LEFT JOIN Suppliers s ON p.supplier_id = s.supplier_id 
          WHERE p.supplier_id IS NOT NULL
          ORDER BY p.product_id DESC";

$deliveries = $conn->query($query);
?>

<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Supplier Log Matrix</h1>
        <p class="text-sm text-slate-400">Track source origins, inbound line items, and batch delivery timestamps.</p>
    </div>

    <?= $action_msg ?>

    <?php if ($_SESSION['role'] === 'Admin'): ?>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <h3 class="text-sm font-bold uppercase text-slate-400 mb-4">Register New Supply Partner</h3>
        <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Supplier / Company Name</label>
                <input type="text" name="supplier_name" placeholder="e.g. Nexus Distributors" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Contact Details (Optional)</label>
                <input type="text" name="contact_info" placeholder="e.g. info@nexus.co.ke" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <button type="submit" name="add_supplier" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all shadow-md">Save Supplier Profile</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800 text-xs font-bold uppercase text-slate-400 border-b border-slate-200 dark:border-slate-700">
                    <th class="p-4">Supplier Partner</th>
                    <th class="p-4">Product Consignment</th>
                    <th class="p-4">Quantity Supplied</th>
                    <th class="p-4">Registration / Delivery Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                <?php if ($deliveries && $deliveries->num_rows > 0): ?>
                    <?php while ($d = $deliveries->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                            <td class="p-4 font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($d['supplier_name']) ?></td>
                            <td class="p-4 font-medium text-emerald-600 dark:text-emerald-400"><?= htmlspecialchars($d['product_name']) ?></td>
                            <td class="p-4 font-mono font-bold"><?= number_format($d['stock_quantity']) ?> units</td>
                            <td class="p-4 text-slate-500 dark:text-slate-400">
                                <?= date('M d, Y — h:i A', strtotime($d['delivery_date'])) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="p-8 text-center text-slate-400 italic">
                            No active supplier product assignments found in database logs.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>