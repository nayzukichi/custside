<?php
$products = json_decode(file_get_contents('../data/products.json'), true);
$active_cat = $_GET['cat'] ?? 'espresso';
$categories = [
    'espresso' => ['label' => 'Espresso Blends', 'items' => []],
    'pastries' => ['label' => 'Fresh Pastries', 'items' => []],
    'nocoffee' => ['label' => 'Cold Drinks', 'items' => []]
];
foreach ($products as $p) {
    if (isset($categories[$p['category']])) {
        $categories[$p['category']]['items'][] = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu — King's Cup Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-inner">
        <a href="index.php" class="nav-logo">
            <div class="logo-icon"><svg width="36" height="36" viewBox="0 0 36 36"><circle cx="18" cy="18" r="18" fill="#3b1f0e"/><path d="M10 22 Q12 14 18 12 Q24 14 26 22 Q22 26 18 27 Q14 26 10 22Z" fill="#c8860a"/><circle cx="18" cy="19" r="4" fill="#3b1f0e"/></svg></div>
            <span class="logo-text">King's Cup</span>
        </a>
        <ul class="nav-links">
            <li><a href="menu.php" class="active">Menu</a></li>
            <li><a href="view_orders.php">View Orders</a></li>
            <li><a href="order_status.php">Order Status</a></li>
            <li><a href="order_history.php">Order History</a></li>
        </ul>
        <a href="login.php" class="btn-login">Login / Sign Up</a>
        <button class="hamburger" id="hamburger">☰</button>
    </div>
</nav>
<section class="menu-hero">
    <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=1200&q=80" alt="Coffee Menu" class="menu-hero-bg">
    <div class="menu-hero-overlay"></div>
    <div class="menu-hero-content">
        <p class="menu-label">King's Cup Menu</p>
        <h1>Brewed With Passion</h1>
        <p class="menu-desc">Explore handcrafted espresso drinks, refreshing beverages, and freshly baked pastries.</p>
    </div>
</section>
<section class="menu-page">
    <aside class="menu-sidebar">
        <h2>Categories</h2>
        <a href="menu.php?cat=espresso" class="menu-category <?= $active_cat === 'espresso' ? 'menu-category-active' : '' ?>">Espresso Blends</a>
        <a href="menu.php?cat=pastries" class="menu-category <?= $active_cat === 'pastries' ? 'menu-category-active' : '' ?>">Fresh Pastries</a>
        <a href="menu.php?cat=nocoffee" class="menu-category <?= $active_cat === 'nocoffee' ? 'menu-category-active' : '' ?>">Cold Drinks</a>
    </aside>
    <div class="menu-products">
        <div class="menu-top"><h2><?= htmlspecialchars($categories[$active_cat]['label']) ?></h2></div>
        <div class="menu-products-grid">
            <?php foreach ($categories[$active_cat]['items'] as $item): ?>
            <div class="menu-card">
                <div class="card-img-wrap"><img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"></div>
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p><?= htmlspecialchars($item['desc']) ?></p>
                <div class="menu-card-bottom">
                    <span class="menu-price">₱<?= number_format($item['price']) ?></span>
                    <a href="order.php?id=<?= $item['id'] ?>&cat=<?= urlencode($active_cat) ?>" class="btn-check">Order Now</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<footer class="footer"><div class="footer-inner"><div class="footer-contact"><p class="footer-label">Contact us via:</p><p>vibri@gmail.com</p><p>King's Cup Coffee</p></div><div class="footer-copy"><p>&copy; <?= date('Y') ?> King's Cup Coffee Enterprises. All rights reserved.</p></div></div></footer>
<script src="main.js"></script>
</body>
</html>