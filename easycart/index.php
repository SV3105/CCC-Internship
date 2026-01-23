<?php 
$title = "EasyCart India - The Big Sale is Live!";
$base_path = "./";
$page = "home";
include './includes/products_data.php';
include './includes/header.php'; 
?>

    <!-- New Hero Section: Split Layout -->
    <div class="hero-split">
        <div class="hero-content">
            <span class="badge">SEASONAL OFFER</span>
            <h2>Style Meets <br>Innovation</h2>
            <p>Discover the latest trends in fashion and technology with exclusive deals.</p>
            <div class="hero-btns">
                <a href="./php/products.php" class="btn">Shop Now</a>
                <a href="./php/products.php?category=fashion" class="btn btn-outline">Trending</a>
            </div>
        </div>
        <div class="hero-image">
            <div class="hero-composition">
                <div class="hero-circle-bg"></div>
                <div class="floating-pill pill-1">
                    <i class="fas fa-bolt" style="color: #ffb400;"></i> Fast Shipping
                </div>
                <div class="floating-pill pill-2">
                    <i class="fas fa-tag"></i> 50% OFF
                </div>
                <div class="hero-visual-card">
                    <div class="visual-img-placeholder">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="visual-content">
                        <h4>Ultra Boost 5.0</h4>
                        <p>Comfort Redefined</p>
                        <div class="visual-price">₹2,999</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Main Content Card -->
        <div class="page-content hero-overlap">
            
            <!-- Trending Categories (Images) -->
            <section class="section">
                <div class="section-header">
                    <h2>Shop By Category</h2>
                    <a href="./php/products.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="categories-grid">
                    <?php 
                    // Use $categories from includes/products_data.php
                    foreach($categories as $cat_slug => $cat_data): 
                    ?>
                    <div class="category-card-img">
                        <a href="./php/products.php?category=<?php echo $cat_slug; ?>">
                            <img src="./images/<?php echo $cat_data['image']; ?>" alt="<?php echo $cat_data['name']; ?>">
                            <div class="cat-overlay">
                                <h3><?php echo $cat_data['name']; ?></h3>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Featured Products -->
            <section class="section section-blockbuster">
                <h2>Blockbuster Deals</h2>
                <div class="products-grid">
                    <?php foreach($products as $product): ?>
                        <?php if(isset($product['featured']) && $product['featured']): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="./images/<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>">
                            </div>
                            <h3><?php echo $product['title']; ?></h3>
                            <p class="price">₹<?php echo $product['price']; ?> 
                                <?php if($product['old_price']): ?>
                                <span class="old-price">₹<?php echo $product['old_price']; ?></span>
                                <?php endif; ?>
                            </p>
                            <a href="./php/<?php echo $product['url']; ?>" class="btn">View Details</a>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Top Indian Brands (Clickable) -->
            <section class="section">
                <h2>Top Indian Brands</h2>
                <div class="brands-grid">
                    <a href="./php/products.php?brand=boat" class="brand-card">
                        <h3 class="brand-boat">boAt</h3>
                        <p>Audio Wearables</p>
                    </a>
                    <a href="./php/products.php?brand=fabindia" class="brand-card">
                        <h3 class="brand-fabindia">FabIndia</h3>
                        <p>Ethnic Fashion</p>
                    </a>
                    <a href="./php/products.php?brand=mamaearth" class="brand-card">
                        <h3 class="brand-mamaearth">Mamaearth</h3>
                        <p>Toxin Free</p>
                    </a>
                    <a href="./php/products.php?brand=titan" class="brand-card">
                        <h3 class="brand-titan">Titan</h3>
                        <p>Timeless Watches</p>
                    </a>
                    <a href="./php/products.php?brand=hrx" class="brand-card">
                        <h3 class="brand-hrx">HRX</h3>
                        <p>Fitness Gear</p>
                    </a>
                    <a href="./php/products.php?brand=noise" class="brand-card">
                        <h3 class="brand-noise">Noise</h3>
                        <p>Smart Tech</p>
                    </a>
                </div>
            </section>

        </div>
    </div>

<?php include './includes/footer.php'; ?>