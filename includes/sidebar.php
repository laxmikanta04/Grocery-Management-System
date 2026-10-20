<?php
$activePage = $activePage ?? '';
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link">
        <span class="brand-image"><i class="fa-solid fa-bag-shopping ms-2"></i></span>
        <span class="brand-text font-weight-light ms-2">Grocery Admin</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php echo ($activePage === 'dashboard') ? 'active' : ''; ?>">
                        <i class="nav-icon fa-solid fa-gauge"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="products.php" class="nav-link <?php echo ($activePage === 'products') ? 'active' : ''; ?>">
                        <i class="nav-icon fa-solid fa-boxes-stacked"></i>
                        <p>Products</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="purchases.php" class="nav-link <?php echo ($activePage === 'purchases') ? 'active' : ''; ?>">
                        <i class="nav-icon fa-solid fa-cart-plus"></i>
                        <p>Purchases</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="sales.php" class="nav-link <?php echo ($activePage === 'sales') ? 'active' : ''; ?>">
                        <i class="nav-icon fa-solid fa-cash-register"></i>
                        <p>Sales</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="inventory.php" class="nav-link <?php echo ($activePage === 'inventory') ? 'active' : ''; ?>">
                        <i class="nav-icon fa-solid fa-warehouse"></i>
                        <p>Inventory</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gst.php" class="nav-link <?php echo ($activePage === 'gst') ? 'active' : ''; ?>">
                        <i class="nav-icon fa-solid fa-percent"></i>
                        <p>GST Reports</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
