<?php  
include "db.php";  
?>

<div class="all-products-wrapper">
    <h2 class="mb-3"><i class="fa-solid fa-clipboard-list me-2"></i> Stock Summary</h2>
    <p>
        Overview of all items in <strong>Tindahan ni Lola's</strong> inventory.  
        Products with low or no stock are automatically highlighted.
    </p>

    <div class="card p-3 shadow-sm mt-4">
        <?php
        $low_threshold = 10;

        $q = $conn->query("SELECT * FROM inventory ORDER BY id ASC");

        if ($q && $q->num_rows > 0):
            $low_count = 0;
            $no_stock_count = 0;

            $products = [];
            while ($r = $q->fetch_assoc()) {
                $r['remaining_stock'] = (int)$r['remaining_stock'];
                if ($r['remaining_stock'] == 0) {
                    $no_stock_count++;
                } elseif ($r['remaining_stock'] <= $low_threshold) {
                    $low_count++;
                }
                $products[] = $r;
            }
        ?>

            <?php if ($no_stock_count > 0): ?>
                <div class="alert alert-danger mb-3 fw-bold">
                    ❌ Notice: <?= $no_stock_count ?> product<?= $no_stock_count > 1 ? 's have' : ' has' ?> NO STOCK LEFT.
                </div>
            <?php endif; ?>

            <?php if ($low_count > 0): ?>
                <div class="alert alert-warning mb-3 fw-bold">
                    ⚠️ Notice: <?= $low_count ?> product<?= $low_count > 1 ? 's are' : ' is' ?> running low on stock.
                </div>
            <?php endif; ?>

            <?php if ($low_count == 0 && $no_stock_count == 0): ?>
                <div class="alert alert-success mb-3 fw-bold">
                    ✅ All products have sufficient stock levels.
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle text-center">
                    <thead class="table-success">
                        <tr>
                            <th>Code</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Date Acquired</th>
                            <th>Total Stock</th>
                            <th>Remaining Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $r): 
                            $status = '';
                            $row_class = '';
                            if ($r['remaining_stock'] == 0) {
                                $status = '❌ No Stock';
                                $row_class = 'table-danger fw-bold';
                            } elseif ($r['remaining_stock'] <= $low_threshold) {
                                $status = '⚠️ Low Stock';
                                $row_class = 'table-warning fw-bold';
                            } else {
                                $status = '🟢 In Stock';
                            }
                        ?>
                            <tr class="<?= $row_class ?>">
                                <td><?= htmlspecialchars($r['code']) ?></td>
                                <td><?= htmlspecialchars($r['item_name']) ?></td>
                                <td><?= htmlspecialchars($r['category']) ?></td>
                                <td><?= htmlspecialchars($r['date_acquired']) ?></td>
                                <td><?= (int)$r['total_stock'] ?></td>
                                <td><?= (int)$r['remaining_stock'] ?></td>
                                <td><?= $status ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <div class="alert alert-info text-center mt-3">
                📦 No products found in the inventory.
            </div>
        <?php endif; ?>
    </div>
</div>