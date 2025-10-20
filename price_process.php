<?php
include "db.php";

if (isset($_POST['update_price'])) {
    $code = trim($_POST['code']);
    $new_price = floatval($_POST['new_price']);
    $cost_price = floatval($_POST['cost_price']);
    $effective_date = trim($_POST['effective_date']);

    if (empty($code) || empty($new_price) || empty($cost_price) || empty($effective_date)) {
        echo "<script>alert('⚠️ Please fill in all fields.'); window.history.back();</script>";
        exit;
    }

    $check = $conn->prepare("SELECT id FROM inventory WHERE code = ?");
    $check->bind_param("s", $code);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('❌ Product code not found. Please enter a valid code.'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("UPDATE inventory SET price = ?, cost_price = ?, price_updated = ? WHERE code = ?");
    $stmt->bind_param("ddss", $new_price, $cost_price, $effective_date, $code);

    if ($stmt->execute()) {
        echo "<script>
            alert('✅ Price and cost price updated successfully!');
            window.location.href='index.php';
        </script>";
    } else {
        echo "<script>
            alert('❌ Failed to update price. Please try again.');
            window.history.back();
        </script>";
    }

    $stmt->close();
    $check->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit;
}
?>