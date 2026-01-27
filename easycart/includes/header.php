<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'EasyCart India'; ?></title>
    <?php $v = time(); ?>
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/styles.css?v=<?php echo $v; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php if(isset($extra_css)): ?>
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/<?php echo $extra_css; ?>?v=<?php echo $v; ?>">
    <?php endif; ?>
    <?php if(isset($extra_css_2)): ?>
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/<?php echo $extra_css_2; ?>?v=<?php echo $v; ?>">
    <?php endif; ?>
</head>
<body>
    <header>
        <nav>
            <h1>EasyCart</h1>
            <form action="<?php echo $base_path; ?>php/products.php" method="GET" class="nav-search">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <ul>
                <li><a href="<?php echo $base_path; ?>index.php" class="<?php echo ($page === 'home') ? 'active-nav' : ''; ?>">Home</a></li>
                <li><a href="<?php echo $base_path; ?>php/products.php" class="<?php echo ($page === 'products') ? 'active-nav' : ''; ?>">Collections</a></li>
                <li><a href="<?php echo $base_path; ?>php/cart.php" class="<?php echo ($page === 'cart') ? 'active-nav' : ''; ?>">Cart <i class="fas fa-shopping-cart"></i></a></li>
                <li><a href="<?php echo $base_path; ?>php/auth.php" class="<?php echo ($page === 'auth') ? 'active-nav' : ''; ?>">Login / Sign Up</a></li>
                <li><a href="<?php echo $base_path; ?>php/orders.php" class="<?php echo ($page === 'orders') ? 'active-nav' : ''; ?>">Orders</a></li>
                <li><a href="<?php echo $base_path; ?>php/wishlist.php" class="<?php echo ($page === 'wishlist') ? 'active-nav' : ''; ?>"><i class="far fa-heart"></i> Wishlist</a></li>
                <li><a href="<?php echo $base_path; ?>php/profile.php" class="nav-profile-link <?php echo ($page === 'profile') ? 'active-nav' : ''; ?>" title="My Profile"><i class="fas fa-user-circle"></i></a></li>
            </ul>
        </nav>
    </header>
    <script src="<?php echo $base_path; ?>js/wishlist.js"></script>
