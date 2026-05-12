<?php
session_start();
$products = json_decode(file_get_contents('../data/products.json'), true);
$pid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$product = null;
foreach ($products as $p) {
    if ($p['id'] == $pid) { $product = $p; break; }
}
if (!$product) { header('Location: menu.php'); exit; }

$sizes = [
    'gato'   => ['label'=>'Gato (16 oz.)', 'price'=>150],
    'grande' => ['label'=>'Grande (20 oz.)','price'=>175],
    'venti'  => ['label'=>'Venti (24 oz.)', 'price'=>195]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_order'])) {
    $pid_post = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $size_key = $_POST['size'] ?? 'gato';
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    $selected_product = null;
    foreach ($products as $p) { if ($p['id'] == $pid_post) { $selected_product = $p; break; } }
    if ($selected_product && ($selected_product['type'] !== 'drink' || isset($sizes[$size_key]))) {
        $price = ($selected_product['type'] === 'drink') ? $sizes[$size_key]['price'] : $selected_product['price'];
        $size_label = ($selected_product['type'] === 'drink') ? $sizes[$size_key]['label'] : 'Regular';
        $order_key = $pid_post . '_' . ($selected_product['type'] === 'drink' ? $size_key : 'n/a');
        if (!isset($_SESSION['orders'])) $_SESSION['orders'] = [];
        if (isset($_SESSION['orders'][$order_key])) {
            $_SESSION['orders'][$order_key]['quantity'] += $qty;
        } else {
            $_SESSION['orders'][$order_key] = [
                'product_id' => $pid_post,
                'name' => $selected_product['name'],
                'desc' => $selected_product['desc'],
                'img' => $selected_product['image'],
                'size_label' => $size_label,
                'price' => $price,
                'quantity' => $qty,
                'added_at' => date('Y-m-d H:i:s')
            ];
        }
        header('Location: view_orders.php'); exit;
    }
}
$is_drink = ($product['type'] === 'drink');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order — <?= htmlspecialchars($product['name']) ?> | King's Cup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar"><div class="nav-inner"><a href="index.php" class="nav-logo"><span class="logo-text">King's Cup</span></a><ul class="nav-links"><li><a href="menu.php">Menu</a></li><li><a href="view_orders.php">View Orders</a></li></ul></div></nav>
<div class="order-page">
    <a href="menu.php?cat=<?= urlencode($product['category']) ?>" class="btn-back">&#8249; Back</a>
    <div class="order-layout">
        <div class="order-preview">
            <div class="order-preview-img"><img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"></div>
            <div class="order-preview-content"><h2><?= htmlspecialchars($product['name']) ?></h2><p><?= htmlspecialchars($product['desc']) ?></p></div>
        </div>
        <div class="order-form-wrap">
            <form method="POST" action="order.php?id=<?= $pid ?>">
                <input type="hidden" name="product_id" value="<?= $pid ?>">
                <input type="hidden" name="add_order" value="1">
                <?php if ($is_drink): ?>
                <div class="order-field"><label>SIZE</label><div class="size-options">
                    <?php foreach ($sizes as $key => $size): ?>
                    <label class="size-card"><input type="radio" name="size" value="<?= $key ?>" <?= $key==='gato'?'checked':'' ?> onchange="updateTotal()"><span class="size-name"><?= $size['label'] ?></span><span class="size-price">₱<?= $size['price'] ?></span></label>
                    <?php endforeach; ?>
                </div></div>
                <?php endif; ?>
                <div class="order-field"><label>QUANTITY</label><input type="number" name="quantity" id="qty-input" value="1" min="1" max="20" onchange="updateTotal()"></div>
                <button type="submit" class="btn-add-order">ADD TO ORDER</button>
            </form>
        </div>
    </div>
</div>
<script>
const SIZES = <?= json_encode(array_column($sizes, 'price')) ?>;
const IS_DRINK = <?= $is_drink ? 'true' : 'false' ?>;
function updateTotal() { /* handled by main.js */ }
</script>
<script src="main.js"></script>
</body>
</html>