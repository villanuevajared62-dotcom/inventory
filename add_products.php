<?php 
include "db.php"; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    header('Content-Type: application/json');
    
    ob_clean();
    
    $action = $_POST['action'];

    if ($action === 'add') {
        $code = trim($_POST['code']);
        $item = trim($_POST['item_name']);
        $category = trim($_POST['category']);
        $date = $_POST['date_acquired'];
        $stock = (int)$_POST['total_stock'];

        // Debug log
        error_log("Adding product: $code - $item");

        $stmt = $conn->prepare("INSERT INTO inventory (code, item_name, category, date_acquired, total_stock, remaining_stock)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $code, $item, $category, $date, $stock, $stock);
        
        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            
            // Debug log
            error_log("Insert successful. New ID: $newId");
            
            $response = [
                'success' => true, 
                'id' => $newId,
                'code' => htmlspecialchars($code),
                'item_name' => htmlspecialchars($item),
                'category' => htmlspecialchars($category),
                'date_acquired' => htmlspecialchars($date),
                'total_stock' => $stock,
                'remaining_stock' => $stock
            ];
            
            echo json_encode($response);
        } else {
            error_log("Insert failed: " . $stmt->error);
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
        exit;
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        error_log("Deleting product ID: $id");
        
        $stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        
        echo json_encode(['success' => $success]);
        $stmt->close();
        exit;
    }

    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $code = trim($_POST['code']);
        $item = trim($_POST['item_name']);
        $category = trim($_POST['category']);
        $date = $_POST['date_acquired'];
        $stock = (int)$_POST['total_stock'];

        error_log("Editing product ID: $id");

        $stmt = $conn->prepare("UPDATE inventory SET code=?, item_name=?, category=?, date_acquired=?, total_stock=?, remaining_stock=? WHERE id=?");
        $stmt->bind_param("ssssiii", $code, $item, $category, $date, $stock, $stock, $id);
        $success = $stmt->execute();
        
        echo json_encode(['success' => $success]);
        $stmt->close();
        exit;
    }
}
?>

<div id="inventory-section" class="container py-4">
    <div class="inventoryMenu-wrapper">
        <h1 class="text-center">Tindahan ni Lola - Inventory</h1>
        <p class="text-center">Last Updated: <span id="last-updated"></span></p>

        <div class="add_products-wrapper mt-4">
            <div class="card p-3 mb-4 shadow-sm">
                <h4>Add New Product</h4>
                <div id="msg" class="mb-2"></div>
                <form id="addProductForm" method="POST">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <input type="text" name="code" class="form-control" placeholder="Code" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="item_name" class="form-control" placeholder="Item Name" required>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_acquired" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="total_stock" class="form-control" placeholder="Total Stock" required>
                        </div>
                        <div class="col-md-2">
                            <select name="category" id="category" class="form-select" required>
                                <option value="" disabled selected>Select Category</option>
                                <option>Food</option>
                                <option>Beverages</option>
                                <option>Condiments</option>
                                <option>Snacks</option>
                                <option>Canned Goods</option>
                                <option>Toiletries</option>
                                <option>Household Items</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="col-md-2" id="other-category" style="display:none;">
                            <input type="text" id="custom-category" class="form-control" placeholder="Enter category">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-success w-100">Add</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card p-3 shadow-sm">
                <h4>Inventory List</h4>
                <table id="inventory-table" class="table table-bordered table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Code</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Date Acquired</th>
                            <th>Total Stock</th>
                            <th>Remaining Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inventory-body">
                        <?php
                        $result = $conn->query("SELECT * FROM inventory ORDER BY id ASC");
                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                        ?>
                            <tr data-id="<?= $row['id'] ?>">
                                <td><?= htmlspecialchars($row['code']) ?></td>
                                <td><?= htmlspecialchars($row['item_name']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td><?= htmlspecialchars($row['date_acquired']) ?></td>
                                <td><?= (int)$row['total_stock'] ?></td>
                                <td><?= (int)$row['remaining_stock'] ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm btn-edit">✏️ Edit</button>
                                    <button class="btn btn-danger btn-sm btn-delete">🗑️ Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr id="no-products-row"><td colspan="7" class="text-center text-muted">No products found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Edit Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editProductForm">
            <input type="hidden" name="id" id="edit-id">
            <div class="mb-2">
                <label>Code</label>
                <input type="text" class="form-control" name="code" id="edit-code" required>
            </div>
            <div class="mb-2">
                <label>Item Name</label>
                <input type="text" class="form-control" name="item_name" id="edit-name" required>
            </div>
            <div class="mb-2">
                <label>Category</label>
                <input type="text" class="form-control" name="category" id="edit-category" required>
            </div>
            <div class="mb-2">
                <label>Date Acquired</label>
                <input type="date" class="form-control" name="date_acquired" id="edit-date" required>
            </div>
            <div class="mb-2">
                <label>Total Stock</label>
                <input type="number" class="form-control" name="total_stock" id="edit-stock" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Update current date
    document.getElementById("last-updated").textContent =
        new Date().toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: '2-digit'});

    // Show input field when "Others" is selected
    $('#category').on('change', function() {
        if ($(this).val() === 'Others') {
            $('#other-category').show();
            $('#custom-category').prop('required', true);
        } else {
            $('#other-category').hide();
            $('#custom-category').prop('required', false);
        }
    });

    // ADD PRODUCT - AJAX with FULL DEBUG
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();
        const msg = $('#msg');
        msg.text('Adding...').css('color', 'gray');

        let category = $('#category').val();
        if (category === 'Others') {
            category = $('#custom-category').val().trim();
            if (!category) {
                msg.text('⚠️ Please enter a category.').css('color', 'red');
                return;
            }
        }

        const formData = {
            action: 'add',
            code: $('input[name="code"]').val().trim(),
            item_name: $('input[name="item_name"]').val().trim(),
            date_acquired: $('input[name="date_acquired"]').val(),
            total_stock: $('input[name="total_stock"]').val(),
            category: category
        };

        console.log('📤 Sending data:', formData);

        $.ajax({
            url: 'add_products.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('📥 Response received:', response);
                
                if (response.success) {
                    msg.text('✅ Product added successfully!').css('color', 'green');
                    $('#addProductForm')[0].reset();
                    $('#other-category').hide();
                    $('#custom-category').prop('required', false);

                    $('#no-products-row').remove();

                    const newRow = `
                        <tr data-id="${response.id}">
                            <td>${response.code}</td>
                            <td>${response.item_name}</td>
                            <td>${response.category}</td>
                            <td>${response.date_acquired}</td>
                            <td>${response.total_stock}</td>
                            <td>${response.remaining_stock}</td>
                            <td>
                                <button class="btn btn-warning btn-sm btn-edit">✏️ Edit</button>
                                <button class="btn btn-danger btn-sm btn-delete">🗑️ Delete</button>
                            </td>
                        </tr>
                    `;
                    
                    console.log('➕ Adding row with ID:', response.id);
                    $('#inventory-body').append(newRow);
                    
                    setTimeout(() => msg.text(''), 2000);
                } else {
                    console.error('❌ Server returned error:', response.error);
                    msg.text('❌ Error: ' + (response.error || 'Unknown error')).css('color', 'red');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                msg.text('❌ Error adding product. Check console.').css('color', 'red');
            }
        });
    });

    // DELETE PRODUCT
    $(document).on('click', '.btn-delete', function() {
        if (!confirm('⚠️ Delete this item?')) return;
        
        const id = $(this).closest('tr').data('id');
        console.log('🗑️ Deleting ID:', id);
        const row = $(this).closest('tr');
        
        $.ajax({
            url: 'add_products.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            dataType: 'json',
            success: function(response) {
                console.log('📥 Delete response:', response);
                if (response.success) {
                    row.remove();
                    if ($('#inventory-body tr').length === 0) {
                        $('#inventory-body').append('<tr id="no-products-row"><td colspan="7" class="text-center text-muted">No products found.</td></tr>');
                    }
                } else {
                    alert('❌ Error deleting product.');
                }
            },
            error: function(xhr) {
                console.error('❌ Delete error:', xhr.responseText);
                alert('❌ Error deleting product.');
            }
        });
    });

    // Cache modal element for better performance and fix BS5 constructor
    const editModalEl = document.getElementById('editModal');
    const editModal = new bootstrap.Modal(editModalEl);

    // EDIT PRODUCT - Open modal
    $(document).on('click', '.btn-edit', function() {
        const row = $(this).closest('tr');
        const id = row.data('id');
        console.log('✏️ Editing ID:', id);
        
        $('#edit-id').val(id);
        $('#edit-code').val(row.find('td:eq(0)').text().trim());
        $('#edit-name').val(row.find('td:eq(1)').text().trim());
        $('#edit-category').val(row.find('td:eq(2)').text().trim());
        $('#edit-date').val(row.find('td:eq(3)').text().trim());
        $('#edit-stock').val(row.find('td:eq(4)').text().trim());
        
        // Properly show modal using the instance
        editModal.show();
    });

    // EDIT PRODUCT - Save changes
    $('#editProductForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit-id').val();
        const updated = {
            action: 'edit',
            id: id,
            code: $('#edit-code').val().trim(),
            item_name: $('#edit-name').val().trim(),
            category: $('#edit-category').val().trim(),
            date_acquired: $('#edit-date').val(),
            total_stock: $('#edit-stock').val()
        };

        console.log('📝 Updating:', updated);

        $.ajax({
            url: 'add_products.php',
            type: 'POST',
            data: updated,
            dataType: 'json',
            success: function(response) {
                console.log('📥 Update response:', response);
                if (response.success) {
                    const row = $(`tr[data-id="${id}"]`);
                    row.find('td:eq(0)').text(updated.code);
                    row.find('td:eq(1)').text(updated.item_name);
                    row.find('td:eq(2)').text(updated.category);
                    row.find('td:eq(3)').text(updated.date_acquired);
                    row.find('td:eq(4)').text(updated.total_stock);
                    row.find('td:eq(5)').text(updated.total_stock);
                    
                    // Properly hide modal using the instance
                    editModal.hide();
                } else {
                    alert('❌ Error updating product.');
                }
            },
            error: function(xhr) {
                console.error('❌ Update error:', xhr.responseText);
                alert('❌ Error updating product.');
            }
        });
    });

    // Additional fix: Listen for modal hidden event to ensure cleanup (prevents backdrop issues)
    editModalEl.addEventListener('hidden.bs.modal', function () {
        console.log('Modal fully closed and cleaned up.');
        // Optional: Reset form if needed
        $('#editProductForm')[0].reset();
    });
</script>