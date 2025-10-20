<?php
include "db.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('No product selected.'); window.location.href='index.php';</script>";
    exit;
}

$id = (int)$_GET['id'];
$res = $conn->query("SELECT * FROM inventory WHERE id = $id");
if ($res->num_rows === 0) {
    echo "<script>alert('Product not found.'); window.location.href='index.php';</script>";
    exit;
}

$row = $res->fetch_assoc();
?>

<div class="card w-100 p-4 shadow-sm">
    <h4 class="mb-3 text-center">✏️ Edit Price for <?= htmlspecialchars($row['item_name']) ?></h4>

    <form method="POST" action="price_process.php">
        <input type="hidden" name="code" value="<?= htmlspecialchars($row['code']) ?>">
        <div class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="number" step="0.01" name="new_price" class="form-control" placeholder="New Price (₱)" required>
            </div>
            <div class="col-md-4">
                <input type="date" name="effective_date" class="form-control" required>
            </div>
            <div class="col-md-4">
                <button type="submit" name="update_price" class="btn btn-success w-100">Save Changes</button>
            </div>
        </div>
    </form>
</div>