<aside class="w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col justify-between z-10">
    <div class="p-6">
        <div class="flex items-center gap-3 mb-8">
            <div class="p-2 bg-emerald-600 text-white rounded-xl"><i class="fa-solid to fa-boxes-stacked text-xl"></i></div>
            <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">StockCore</span>
        </div>
        <nav class="space-y-1">
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all"><i class="fa-solid fa-chart-pie w-5"></i> Dashboard</a>
            <a href="products.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all"><i class="fa-solid fa-laptop w-5"></i> Products</a>
            <a href="sales.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all"><i class="fa-solid fa-cart-shopping w-5"></i> Sales Panel</a>
            <a href="suppliers.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all"><i class="fa-solid fa-truck-field w-5"></i> Suppliers</a>
        </nav>
    </div>
    <div class="p-6 border-t border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold uppercase"><?= substr($_SESSION['username'], 0, 2) ?></div>
            <div>
                <h4 class="text-sm font-semibold truncate max-w-[120px]"><?= htmlspecialchars($_SESSION['username']) ?></h4>
                <span class="text-xs text-slate-400 block capitalize"><?= $_SESSION['role'] ?></span>
            </div>
        </div>
    </div>
</aside>

<div class="flex-1 flex flex-col min-w-0 overflow-hidden">
    <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 z-10">
        <div class="font-semibold text-lg text-slate-500 capitalize">Role: <span class="text-slate-900 dark:text-white font-bold"><?= $_SESSION['role'] ?></span></div>
        <div class="flex items-center gap-4">
            <button id="themeToggle" class="p-2 text-slate-500 hover:text-emerald-500 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all"><i class="fa-solid fa-moon text-lg dark:hidden"></i><i class="fa-solid fa-sun text-lg hidden dark:block"></i></button>
            
            <div class="relative">
                <button id="notiBtn" class="p-2 text-slate-500 hover:text-emerald-500 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all relative">
                    <i class="fa-solid fa-bell text-lg"></i>
                    <?php if (count($alerts) > 0): ?><span class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 rounded-full animate-pulse"></span><?php endif; ?>
                </button>
                <div id="notiMenu" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl rounded-2xl py-2 overflow-hidden">
                    <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700 text-sm font-bold">System Stock Alerts</div>
                    <div class="max-h-60 overflow-y-auto">
                        <?php if (count($alerts) === 0): ?>
                            <p class="text-xs text-slate-400 p-4 text-center">All product counts healthy.</p>
                        <?php else: ?>
                            <?php foreach ($alerts as $a): ?>
                                <div class="px-4 py-3 border-b border-slate-50 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/30 flex justify-between items-center text-xs">
                                    <span class="font-medium truncate max-w-[180px]"><?= htmlspecialchars($a['name']) ?></span>
                                    <span class="px-2 py-0.5 bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 font-bold rounded">Qty: <?= $a['stock_quantity'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="relative">
                <button id="profileBtn" class="flex items-center gap-1 focus:outline-none"><div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center"><i class="fa-solid fa-user text-slate-500"></i></div></button>
                <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl rounded-xl py-1">
                    <a href="auth/logout.php" class="block px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-all font-semibold"><i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Log Out Account</a>
                </div>
            </div>
        </div>
    </header>
    <main class="flex-1 overflow-y-auto p-8">