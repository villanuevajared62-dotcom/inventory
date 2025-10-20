<?php
include "db.php"; ?>

<div class="card w-100 p-3 shadow-sm">
    <h4 class="mb-3 text-center">💰 Price Management</h4>

    <form method="POST" action="price_process.php" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-2">
                <input type="text" name="code" class="form-control" placeholder="Product Code" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="new_price" step="0.01" class="form-control" placeholder="New Price (₱)" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="cost_price" step="0.01" class="form-control" placeholder="Cost Price (₱)" required>
            </div>
            <div class="col-md-3">
                <input type="date" name="effective_date" class="form-control" required>
            </div>
            <div class="col-md-3">
                <button type="submit" name="update_price" class="btn btn-success w-100">Update Price</button>
            </div>
        </div>
    </form>

    <h5>📋 Current Prices</h5>
    <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>Code</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>Current Price (₱)</th>
                <th>Cost Price (₱)</th>
                <th>Last Updated</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM inventory ORDER BY id ASC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= htmlspecialchars($row['code']) ?></td>
                <td><?= htmlspecialchars($row['item_name']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= isset($row['price']) ? number_format($row['price'], 2) : '0.00' ?></td>
                <td><?= isset($row['cost_price']) ? number_format($row['cost_price'], 2) : '0.00' ?></td>
                <td><?= isset($row['price_updated']) ? htmlspecialchars($row['price_updated']) : '—' ?></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="6" class="text-muted">No products available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>