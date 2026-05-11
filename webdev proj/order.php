<?php
session_start();

/* =============================================
   PRODUCT CATALOG (temporary DB replacement)
============================================= */
$products = [
  1  => ['name'=>'Americano', 'sub'=>'pure strong coffee', 'img'=>'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400&q=80', 'cat'=>'espresso'],
  2  => ['name'=>'Spanish Latte', 'sub'=>'espresso + milk', 'img'=>'https://images.unsplash.com/photo-1578314675249-a6910f80cc4e?w=400&q=80', 'cat'=>'espresso'],
  3  => ['name'=>'Cappuccino', 'sub'=>'espresso + foam', 'img'=>'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400&q=80', 'cat'=>'espresso'],
  4  => ['name'=>'Caramel Macchiato', 'sub'=>'espresso + vanilla + caramel', 'img'=>'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&q=80', 'cat'=>'espresso'],
  5  => ['name'=>'Berry Matcha', 'sub'=>'matcha + strawberry + blueberry', 'img'=>'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&q=80', 'cat'=>'nocoffee'],
  6  => ['name'=>'Hibi-berry Tea', 'sub'=>'hibiscus tea + berry syrup', 'img'=>'https://images.unsplash.com/photo-1582479977511-f0bf69ebeb87?w=400&q=80', 'cat'=>'nocoffee'],
  7  => ['name'=>'Peach Oolong', 'sub'=>'oolong + peach syrup', 'img'=>'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&q=80', 'cat'=>'nocoffee'],
  8  => ['name'=>'Butter Croissant', 'sub'=>'flaky + buttery', 'img'=>'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=400&q=80', 'cat'=>'pastries'],
  9  => ['name'=>'Choco Chocolate', 'sub'=>'dark chocolate + almond', 'img'=>'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80', 'cat'=>'pastries'],
  10 => ['name'=>'Cheese Danish', 'sub'=>'cream cheese + pastry', 'img'=>'https://images.unsplash.com/photo-1571115177098-24ec42ed204d?w=400&q=80', 'cat'=>'pastries'],
];

/* =============================================
   SIZE OPTIONS
============================================= */
$sizes = [
  'gato'   => ['label'=>'Gato (16 oz.)', 'calories'=>8,  'price'=>150],
  'grande' => ['label'=>'Grande (20 oz.)','calories'=>12, 'price'=>175],
  'venti'  => ['label'=>'Venti (24 oz.)', 'calories'=>16, 'price'=>195],
];

/* =============================================
   SAFE PRODUCT LOADING (FIXED)
============================================= */
$pid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$pid || !isset($products[$pid])) {
  header('Location: menu.php');
  exit;
}

$product = $products[$pid];
$back_cat = $product['cat'];

/* =============================================
   ADD TO ORDER HANDLER
============================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_order'])) {

  $pid_post = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
  $size_key = $_POST['size'] ?? 'gato';
  $qty      = max(1, (int)($_POST['quantity'] ?? 1));

  if (isset($products[$pid_post]) && isset($sizes[$size_key])) {

    $product_post = $products[$pid_post];
    $size         = $sizes[$size_key];

    if (!isset($_SESSION['orders'])) {
      $_SESSION['orders'] = [];
    }

    $order_key = $pid_post . '_' . $size_key;

    if (isset($_SESSION['orders'][$order_key])) {
      $_SESSION['orders'][$order_key]['quantity'] += $qty;
    } else {
      $_SESSION['orders'][$order_key] = [
        'order_id'   => uniqid('KC'),
        'product_id' => $pid_post,
        'name'       => $product_post['name'],
        'sub'        => $product_post['sub'],
        'img'        => $product_post['img'],
        'size_key'   => $size_key,
        'size_label' => $size['label'],
        'calories'   => $size['calories'],
        'price'      => $size['price'],
        'quantity'   => $qty,
        'status'     => 'Pending',
        'added_at'   => date('Y-m-d H:i:s'),
      ];
    }

    header('Location: view_orders.php');
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Order — <?= htmlspecialchars($product['name']) ?> | King's Cup</title>

<link rel="stylesheet" href="style.css"/>
</head>

<body>

<nav class="navbar">
  <div class="nav-inner">

    <a href="index.php" class="nav-logo">
      <span class="logo-text">King's Cup</span>
    </a>

    <ul class="nav-links">
      <li><a href="menu.php" class="active">Menu</a></li>
      <li><a href="view_orders.php">View Orders</a></li>
    </ul>

  </div>
</nav>

<div class="order-page">

  <!-- BACK -->
  <a href="menu.php?cat=<?= urlencode($back_cat) ?>" class="btn-back">
    &#8249; Back
  </a>

  <!-- PRODUCT --><div class="order-layout">

  <!-- LEFT: PRODUCT PREVIEW -->
  <div class="order-preview">

    <div class="order-preview-img">
      <img src="<?= htmlspecialchars($product['img']) ?>" alt="Americano coffee cup on wooden table in a cozy cafe setting">
    </div>

    <div class="order-preview-content">
      <h2><?= htmlspecialchars($product['name']) ?></h2>
      <p><?= htmlspecialchars($product['sub']) ?></p>
    </div>

  </div>

  <!-- RIGHT: FORM -->
  <div class="order-form-wrap">

    <div class="order-product-info">
      <img src="<?= htmlspecialchars($product['img']) ?>" alt="Americano coffee cup on wooden table in a cozy cafe setting">
      <h2><?= htmlspecialchars($product['name']) ?></h2>
      <p><?= htmlspecialchars($product['sub']) ?></p>
    </div>

    <!-- FORM -->
    <form method="POST" action="order.php?id=<?= $pid ?>">

      <input type="hidden" name="product_id" value="<?= $pid ?>">
      <input type="hidden" name="add_order" value="1">

      <!-- SIZE -->
      <div class="order-field">
        <label>SIZE</label>

        <?php foreach ($sizes as $key => $size): ?>
          <label>
            <input type="radio" name="size" value="<?= $key ?>" <?= $key==='gato'?'checked':'' ?>>
            <?= $size['label'] ?> (₱<?= $size['price'] ?>)
          </label>
        <?php endforeach; ?>
      </div>

      <!-- QTY -->
      <div class="order-field">
        <label>QUANTITY</label>
        <input type="number" name="quantity" value="1" min="1" max="20">
      </div>

      <button type="submit">ADD TO ORDER</button>

    </form>

  </div>

</div>

<script>
const SIZES = <?= json_encode(array_column($sizes, 'price')) ?>;
</script>

<script src="main.js"></script>

</body>
</html>