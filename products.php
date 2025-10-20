<?php 
include "db.php";
?>

<div class="all-products-wrapper">
    <h2>All Products</h2>
    <p>
      Complete list of items available in <strong>Tindahan ni Lola's</strong> inventory.
    </p>

    <div class="card p-3 shadow-sm mt-4">
        <?php
        $q = $conn->query("SELECT * FROM inventory ORDER BY id ASC");
        if ($q && $q->num_rows):
        ?>
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-success">
                    <tr>
                        <th>Code</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Remaining Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($r = $q->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['code']) ?></td>
                            <td><?= htmlspecialchars($r['item_name']) ?></td>
                            <td><?= htmlspecialchars($r['category']) ?></td>
                            <td><?= (int)$r['remaining_stock'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info text-center mt-3">
                No products found in the inventory.
            </div>
        <?php endif; ?>
    </div>
</div>