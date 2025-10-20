<?php
include "db.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tindahan ni Lola - Inventory Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    
</head>
<body>

<nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand ms-3" href="#">
            <i class="fa-solid fa-store"></i> Inventory System
        </a>

        <div class="dropdown me-4">
            <a class="text-white dropdown-toggle text-decoration-none" href="#" data-bs-toggle="dropdown">
                <i class="fa-solid fa-user-circle"></i> Admin
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                
                <li><a class="dropdown-item text-danger" href="logout.php">Log out</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="sidebar text-white">
    <a href="#" id="btn-dashboard" class="active">
        <i class="fa-solid fa-house me-2"></i> Dashboard
    </a>

    <a class="d-flex justify-content-between align-items-center text-white text-decoration-none" 
       data-bs-toggle="collapse" href="#inventoryMenu" role="button" 
       aria-expanded="false" aria-controls="inventoryMenu">
        <span><i class="fa-solid fa-box me-2"></i> Inventory Management</span>
        <i class="fa-solid fa-chevron-down"></i>
    </a>

    <div class="collapse" id="inventoryMenu">
        <ul class="nav flex-column submenu ms-2 mt-2">
            <li class="nav-item">
                <a href="#" class="nav-link text-white" id="btn-add-products">
                    <i class="fa-solid fa-plus me-2"></i> Add Product
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link text-white" id="btn-price-management">
                    <i class="fa-solid fa-tags me-2"></i> Price Management
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link text-white" id="btn-all-products">
                    <i class="fa-solid fa-list me-2"></i> All Products
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link text-white" id="btn-stock-summary">
                    <i class="fa-solid fa-chart-simple me-2"></i> Stock Summary
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="content">
    <div class="container mt-4" id="content-area">
        <?php
        $low_threshold = 10;

        $query = $conn->query("SELECT * FROM inventory ORDER BY id ASC");
        $low_count = 0; $no_stock_count = 0;
        $total_stock_in = 0; $total_stock_out = 0; $total_remaining = 0;

        if ($query && $query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                $total_stock = (int)$row['total_stock'];
                $remaining = (int)$row['remaining_stock'];
                $stock_out = max($total_stock - $remaining, 0);

                $total_stock_in += $total_stock;
                $total_stock_out += $stock_out;
                $total_remaining += $remaining;

                if ($remaining == 0) $no_stock_count++;
                elseif ($remaining <= $low_threshold) $low_count++;
            }
        }
        ?>

        <div class="all-products-wrapper">
            <h2 class="mb-3"><i class="fa-solid fa-chart-line me-2"></i> Stock Movement Overview</h2>
            <p>
                Real-time overview of product movements in <strong>Tindahan ni Lola's</strong> Inventory.  
                In this site you can see total stock-in, stock-out, and remaining quantities.
            </p>

            <div class="card p-3 shadow-sm mt-4">
                <div class="row text-center mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white p-3 shadow-sm">
                            <h5>Total Stock In</h5>
                            <h3><?= $total_stock_in ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white p-3 shadow-sm">
                            <h5>Total Stock Out</h5>
                            <h3><?= $total_stock_out ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white p-3 shadow-sm">
                            <h5>Total Remaining</h5>
                            <h3><?= $total_remaining ?></h3>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3"><i class="fa-solid fa-clock-rotate-left me-2"></i> Recent Added Products</h5>
                <?php
                $recent = $conn->query("SELECT * FROM inventory ORDER BY id DESC LIMIT 5");
                if ($recent && $recent->num_rows > 0):
                ?>
                <table class="table table-hover align-middle text-center">
                    <thead class="table-secondary">
                        <tr>
                            <th>Item</th>
                            <th>Category</th>
                            <th>Date Added</th>
                            <th>Total Stock</th>
                            <th>Remaining</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = $recent->fetch_assoc()):
                            $status = ($r['remaining_stock'] == 0) ? '❌ No Stock' :
                                      (($r['remaining_stock'] <= $low_threshold) ? '⚠️ Low Stock' : '🟢 In Stock');
                            $row_class = ($r['remaining_stock'] == 0) ? 'table-danger' :
                                         (($r['remaining_stock'] <= $low_threshold) ? 'table-warning' : '');
                        ?>
                        <tr class="<?= $row_class ?>">
                            <td><?= htmlspecialchars($r['item_name']) ?></td>
                            <td><?= htmlspecialchars($r['category']) ?></td>
                            <td><?= htmlspecialchars($r['date_acquired']) ?></td>
                            <td><?= (int)$r['total_stock'] ?></td>
                            <td><?= (int)$r['remaining_stock'] ?></td>
                            <td><?= $status ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="alert alert-info text-center fw-bold">No recent movements found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {

    function loadContent(url) {
        $('#content-area').load(url, function(response, status) {
            if (status === "error") {
                $('#content-area').html("<div class='alert alert-danger mt-5 text-center'>⚠️ Failed to load content.</div>");
            }
        });
    }

    $('#btn-dashboard').on('click', function(e) {
        e.preventDefault();
        location.href = 'index.php';
    });

    $('#btn-add-products').on('click', function(e) {
        e.preventDefault();
        loadContent('add_products.php');
    });

    $('#btn-all-products').on('click', function(e) {
        e.preventDefault();
        loadContent('products.php');
    });

    $('#btn-price-management').on('click', function(e) {
        e.preventDefault();
        loadContent('price_management.php');
    });

    $('#btn-stock-summary').on('click', function(e) {
        e.preventDefault();
        loadContent('stock_summary.php');
    });

    // ✅ REMOVED - Let add_products.php handle edit with modal
    // Edit button is now handled by the loaded page itself

});
</script>
</body>
</html>