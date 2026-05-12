<?php
session_start();
$orders = $_SESSION['orders'] ?? [];
$total = array_sum(array_map(fn($o) => $o['price'] * $o['quantity'], $orders));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_key'])) {
        unset($_SESSION['orders'][$_POST['remove_key']]);
        header('Location: view_orders.php'); exit;
    }
    if (isset($_POST['place_order'])) {
        if (empty($_SESSION['user'])) { header('Location: login.php'); exit; }
        if (!empty($_SESSION['orders'])) {
            $existing = json_decode(file_get_contents('../data/order.json'), true) ?: [];
            $new_id = 'KC-' . str_pad(count($existing)+1, 3, '0', STR_PAD_LEFT);
            $new_order = [
                'id' => $new_id,
                'customer_name' => $_SESSION['user']['name'],
                'customer_email' => $_SESSION['user']['email'],
                'mobile' => $_SESSION['registered_user']['phone'] ?? '',
                'payment_method' => '',
                'total' => $total,
                'status' => 'Pending',
                'type' => 'In-Store',
                'placed_at' => date('Y-m-d H:i:s'),
                'items' => array_values($orders)
            ];
            $existing[] = $new_order;
            file_put_contents('data/orders.json', json_encode($existing, JSON_PRETTY_PRINT));
            $_SESSION['orders'] = [];
            header('Location: payment.php'); exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>View Orders — King's Cup</title><link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="style.css"></head>
<body>
<nav class="navbar"><div class="nav-inner"><a href="index.php" class="nav-logo"><div class="logo-icon"><svg width="36" height="36" viewBox="0 0 36 36"><circle cx="18" cy="18" r="18" fill="#3b1f0e"/><path d="M10 22 Q12 14 18 12 Q24 14 26 22 Q22 26 18 27 Q14 26 10 22Z" fill="#c8860a"/><circle cx="18" cy="19" r="4" fill="#3b1f0e"/></svg></div><span class="logo-text">King's Cup</span></a><ul class="nav-links"><li><a href="menu.php">Menu</a></li><li><a href="view_orders.php" class="active">View Orders</a></li><li><a href="order_status.php">Order Status</a></li><li><a href="order_history.php">Order History</a></li></ul><a href="login.php" class="nav-profile"><svg width="32" height="32" viewBox="0 0 32 32"><circle cx="16" cy="16" r="16" fill="#5c2e0e"/><circle cx="16" cy="13" r="5" fill="#c8860a"/><path d="M6 26c0-5.523 4.477-8 10-8s10 2.477 10 8" fill="#c8860a"/></svg></a><button class="hamburger" id="hamburger">☰</button></div></nav>
<div class="vo-page"><div class="vo-header"><h1><span class="vo-brand">King's Cup</span><span class="vo-divider">|</span> Checkout</h1></div>
<?php if (empty($orders)): ?>
<div class="vo-empty"><div class="vo-empty-icon">☕</div><h2>Your order is empty</h2><p>Head to the menu and pick something delicious.</p><a href="menu.php" class="btn-vo-menu">Browse Menu</a></div>
<?php else: ?>
<div class="vo-table-wrap"><table class="vo-table"><thead><tr><th class="th-product">Products Ordered</th><th>Unit Price</th><th>Quantity</th><th>Item Subtotal</th><th></th></tr></thead><tbody>
<?php foreach ($orders as $key => $item): ?>
<tr><td class="td-product"><img src="<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"><div class="td-info"><span class="td-name"><?= htmlspecialchars($item['name']) ?></span><span class="td-sub"><?= htmlspecialchars($item['size_label']) ?></span></div></td><td>₱<?= number_format($item['price']) ?></td><td><?= $item['quantity'] ?></td><td>₱<?= number_format($item['price'] * $item['quantity']) ?></td><td><form method="POST"><input type="hidden" name="remove_key" value="<?= htmlspecialchars($key) ?>"><button type="submit" class="btn-remove" title="Remove">&#10005;</button></form></td></tr>
<?php endforeach; ?>
</tbody><tfoot><tr><td colspan="3" class="tf-label">Order Total:</td><td class="tf-total">₱<?= number_format($total) ?></td><td></td></tr></tfoot></table></div>
<div class="vo-actions"><a href="menu.php" class="btn-vo-back">&#8249; Add More Items</a><form method="POST"><button type="submit" name="place_order" value="1" class="btn-place-order">Place Order</button></form></div>
<?php endif; ?>
</div>
<footer class="footer"><div class="footer-inner"><div class="footer-contact"><p class="footer-label">Contact us via:</p><p>vibri@gmail.com</p><p>King's Cup Coffee</p></div><div class="footer-copy"><p>&copy; <?= date('Y') ?> King's Cup Coffee Enterprises. All rights reserved.</p></div></div></footer>
<script src="main.js"></script>
</body>
</html>