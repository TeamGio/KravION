<?php
require_once '../config/exempelfil_erp.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_quantity') {
        $medicine_id = $_POST['medicine_id'];
        $quantity = $_POST['quantity'];
        
        $stmt = $conn->prepare("UPDATE medicine_inventory SET quantity = :quantity, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':id', $medicine_id);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Inventory updated successfully!</div>';
        }
    } elseif ($_POST['action'] === 'add_medicine') {
        $medicine_name = $_POST['medicine_name'];
        $medicine_type = $_POST['medicine_type'];
        $quantity = $_POST['quantity'];
        $unit = $_POST['unit'];
        $reorder_level = $_POST['reorder_level'];
        $expiry_date = $_POST['expiry_date'];
        $is_antibiotic = isset($_POST['is_antibiotic']) ? true : false;
        
        $stmt = $conn->prepare("
            INSERT INTO medicine_inventory (medicine_name, medicine_type, quantity, unit, reorder_level, expiry_date, is_antibiotic)
            VALUES (:medicine_name, :medicine_type, :quantity, :unit, :reorder_level, :expiry_date, :is_antibiotic)
        ");
        $stmt->bindParam(':medicine_name', $medicine_name);
        $stmt->bindParam(':medicine_type', $medicine_type);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':unit', $unit);
        $stmt->bindParam(':reorder_level', $reorder_level);
        $stmt->bindParam(':expiry_date', $expiry_date);
        $stmt->bindParam(':is_antibiotic', $is_antibiotic, PDO::PARAM_BOOL);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Medicine added to inventory!</div>';
        }
    }
}

$stmt = $conn->query("
    SELECT * FROM medicine_inventory
    ORDER BY 
        CASE WHEN quantity <= reorder_level THEN 0 ELSE 1 END,
        medicine_name
");
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h3>Medicine Inventory</h3>
    <?php echo $message; ?>
    
    <?php if (count($inventory) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Reorder Level</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventory as $medicine): ?>
                <tr style="<?php echo $medicine['quantity'] <= $medicine['reorder_level'] ? 'background: #F8D7DA;' : ''; ?>">
                    <td>
                        <strong><?php echo htmlspecialchars($medicine['medicine_name']); ?></strong>
                        <?php if ($medicine['is_antibiotic']): ?>
                        <span class="badge badge-warning">Antibiotic</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($medicine['medicine_type'] ?? 'N/A'); ?></td>
                    <td><strong><?php echo $medicine['quantity']; ?> <?php echo htmlspecialchars($medicine['unit'] ?? ''); ?></strong></td>
                    <td><?php echo $medicine['reorder_level']; ?></td>
                    <td><?php echo $medicine['expiry_date'] ? date('Y-m-d', strtotime($medicine['expiry_date'])) : 'N/A'; ?></td>
                    <td>
                        <?php if ($medicine['quantity'] <= $medicine['reorder_level']): ?>
                        <span class="badge badge-danger">Low Stock</span>
                        <?php elseif ($medicine['quantity'] <= $medicine['reorder_level'] * 2): ?>
                        <span class="badge badge-warning">Running Low</span>
                        <?php else: ?>
                        <span class="badge badge-success">In Stock</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="update_quantity">
                            <input type="hidden" name="medicine_id" value="<?php echo $medicine['id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $medicine['quantity']; ?>" style="width: 80px; padding: 4px;" min="0">
                            <button type="submit" class="btn btn-accent" style="padding: 4px 12px; font-size: 0.9em;">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #6C757D; margin-top: 16px;">No medicines in inventory.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Add New Medicine</h3>
    <form method="POST" style="margin-top: 16px;">
        <input type="hidden" name="action" value="add_medicine">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div class="form-group">
                <label>Medicine Name</label>
                <input type="text" name="medicine_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Type</label>
                <input type="text" name="medicine_type" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" class="form-control" required min="0">
            </div>
            <div class="form-group">
                <label>Unit</label>
                <input type="text" name="unit" class="form-control" required placeholder="e.g., tablets">
            </div>
            <div class="form-group">
                <label>Reorder Level</label>
                <input type="number" name="reorder_level" class="form-control" required min="1" value="10">
            </div>
            <div class="form-group">
                <label>Expiry Date</label>
                <input type="date" name="expiry_date" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_antibiotic" value="1">
                This is an antibiotic
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Add Medicine</button>
    </form>
</div>
