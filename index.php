<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Calculate metric calculations for today's current tracking metrics
$today = date('Y-m-d');
$sales_today_res = $conn->query("SELECT SUM(total_amount) AS revenue FROM Sales WHERE DATE(sale_date) = '$today'");
$revenue_today = $sales_today_res->fetch_assoc()['revenue'] ?? 0;

$low_stock_count_res = $conn->query("SELECT COUNT(*) AS total FROM Products WHERE stock_quantity < 5");
$low_stock_count = $low_stock_count_res->fetch_assoc()['total'];

$total_products_res = $conn->query("SELECT COUNT(*) AS total FROM Products");
$total_products = $total_products_res->fetch_assoc()['total'];

// Pull configuration array rows tracking distribution maps across categories for Chart rendering context
$chart_data = $conn->query("SELECT category, COUNT(*) as count FROM Products GROUP BY category");
$categories = []; $counts = [];
while($row = $chart_data->fetch_assoc()){
    $categories[] = $row['category'];
    $counts[] = $row['count'];
}
?>

<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">Performance Overview</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Live tracking metrics for stock allocations.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-5">
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-xl"><i class="fa-solid fa-wallet text-2xl"></i></div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Sales of the Day</span>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Ksh <?= number_format($revenue_today, 2) ?></h3>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-5">
            <div class="p-4 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-xl"><i class="fa-solid fa-triangle-exclamation text-2xl"></i></div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Critical Low Stock Items</span>
                <h3 class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-1"><?= $low_stock_count ?> Products</h3>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-5">
            <div class="p-4 bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 rounded-xl"><i class="fa-solid fa-cubes text-2xl"></i></div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Catalog Item Matrix</span>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1"><?= $total_products ?> Total Stock Keeping Unit</h3>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm max-w-2xl">
        <h3 class="text-md font-bold mb-4">Stock Breakdown by Category Variant</h3>
        <canvas id="categoryChart" class="max-h-72"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($categories) ?>,
            datasets: [{
                label: 'Item Quantities Available',
                data: <?= json_encode($counts) ?>,
                backgroundColor: '#10b981',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>