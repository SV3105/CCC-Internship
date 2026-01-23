<?php
session_start();
$title = "Product Details - EasyCart";
$base_path = "../";
$page = "products";
$extra_css = "product-details.css?v=" . time();
include '../includes/products_data.php';

// Logic to find product
$current_product = null;
if(isset($_GET['id'])) {
    $p_id = (int)$_GET['id'];
    foreach($products as $p) {
        if($p['id'] == $p_id) {
            $current_product = $p;
            break;
        }
    }
}

// Handle Not Found
if(!$current_product) {
    echo "<h2 style='text-align:center; padding: 5rem;'>Product not found. <a href='products.php'>Return to Shop</a></h2>";
    exit;
}

// Update Title to Product Name
$title = $current_product['title'] . " - EasyCart";

include '../includes/header.php';
?>

    <div class="container">
      <div class="page-content layout-transparent product-detail-container">
        <div class="product-detail">
            <div class="product-image">
                <img src="../images/<?php echo $current_product['image']; ?>" alt="<?php echo $current_product['title']; ?>" class="<?php echo isset($current_product['css_class']) ? $current_product['css_class'] : ''; ?>">
            </div>
            <div class="product-info">
            <h2><?php echo $current_product['title']; ?></h2>
            <div class="rating">
                <?php 
                $rating = isset($current_product['rating']) ? $current_product['rating'] : 4.0;
                $full_stars = floor($rating);
                $half_star = $rating - $full_stars >= 0.5;
                
                for($i=1; $i<=5; $i++) {
                    if($i <= $full_stars) {
                        echo '<i class="fas fa-star"></i>';
                    } elseif($i == $full_stars + 1 && $half_star) {
                        echo '<i class="fas fa-star-half-alt"></i>';
                    } else {
                        echo '<i class="far fa-star"></i>';
                    }
                }
                ?>
                <span>(<?php echo $rating; ?> stars)</span>
            </div>
            <p class="price">
                Rs. <?php echo $current_product['price']; ?>
                <?php if(isset($current_product['old_price']) && $current_product['old_price']): ?>
                <span style="text-decoration: line-through; color: #999; font-size: 0.8em; margin-left: 10px;">Rs. <?php echo $current_product['old_price']; ?></span>
                <?php endif; ?>
            </p>
            
            <div class="product-description">
                <h3>Description</h3>
                <p>
                <?php echo isset($current_product['description']) ? $current_product['description'] : 'Product description not available.'; ?>
                </p>
                
                <?php if(isset($current_product['features']) && is_array($current_product['features'])): ?>
                <ul>
                    <?php foreach($current_product['features'] as $feature): ?>
                    <li><?php echo $feature; ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
            
            <div class="product-actions">
                <form action="cart.php" method="POST">
                    <input type="hidden" name="action" value="update_qty">
                    <input type="hidden" name="product_id" value="<?php echo $current_product['id']; ?>">
                    <input type="hidden" name="change" value="1">
                    <button type="submit" class="btn btn-large">Add to Cart</button>
                </form>
            </div>
            </div>
        </div>
      </div>
    </div>

<?php include '../includes/footer.php'; ?>
