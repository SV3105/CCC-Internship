<?php
$title = "Shop - EasyCart India";
$base_path = "../";
$page = "products";
$extra_css = "products.css";
include '../includes/products_data.php';

// --- SERVER SIDE LOGIC ---

// 1. Capture Filter Inputs
$selected_categories = isset($_GET['category']) ? (array)$_GET['category'] : [];
$selected_brands = isset($_GET['brand']) ? (array)$_GET['brand'] : [];
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// 2. Filter Logic
$filtered_products = array_filter($products, function($product) use ($selected_categories, $selected_brands, $search_query) {
    // Check Category
    $cat_match = empty($selected_categories) || in_array($product['category'], $selected_categories);
    // Check Brand
    $brand_match = empty($selected_brands) || in_array($product['brand'], $selected_brands);
    // Check Search
    $search_match = true;
    if ($search_query !== '') {
        $search_match = stripos($product['title'], $search_query) !== false || 
                       stripos($product['category'], $search_query) !== false || 
                       stripos($product['brand'], $search_query) !== false;
    }
    
    return $cat_match && $brand_match && $search_match;
});

// 3. Render Function (HTML Output for Grid Items)
function renderProductsGrid($items) {
    if(!empty($items)):
        foreach($items as $product): ?>
    <div class="product-card">
        <img src="../images/<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>" class="<?php echo isset($product['css_class']) ? $product['css_class'] : ''; ?>">
        <h3><?php echo $product['title']; ?></h3>
        <p class="price">₹<?php echo $product['price']; ?></p>
        <a href="./<?php echo $product['url']; ?>" class="btn">View Details</a>
    </div>
    <?php endforeach; 
    else: ?>
    <div class="no-results" style="display:block;">
        <i class="fas fa-search"></i>
        <p>No products found matching your filters.</p>
    </div>
    <?php endif; 
}

// 4. AJAX HANDLER
// If this is an AJAX request, ONLY output the grid and exit.
if(isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    renderProductsGrid($filtered_products);
    exit;
}

// --- NORMAL PAGE LOAD ---
include '../includes/header.php';
?>

    <div class="container">
        <div class="page-content layout-transparent">
            <h1 class="text-dark mb-4">Explore Collection <span id="headerCount" style="font-size: 1.5rem; color: #888; font-weight: 400;">(<?php echo count($filtered_products); ?>)</span></h1>
            
            <div class="shop-layout">
                <!-- Filters -->
                <aside class="filter-sidebar">
                    <form action="" method="GET" id="filterForm">
                        <div class="filter-group">
                            <h3>Categories</h3>
                            <?php 
                            $cats = ['electronics' => 'Electronics', 'fashion' => 'Fashion', 'home' => 'Home & Living', 'beauty' => 'Beauty'];
                            foreach($cats as $val => $label):
                            ?>
                            <label class="filter-option">
                                <input type="checkbox" name="category[]" value="<?php echo $val; ?>" <?php echo in_array($val, $selected_categories) ? 'checked' : ''; ?>>
                                <span class="checkmark"></span> <?php echo $label; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    
                        <div class="filter-group">
                            <h3>Brands</h3>
                            <?php 
                            $brands = ['boat' => 'boAt', 'noise' => 'Noise', 'hp' => 'HP', 'hrx' => 'HRX', 'fabindia' => 'Fabindia', 'mamaearth' => 'Mamaearth', 'titan' => 'Titan', 'smartphone' => 'Smartphone', 'vip' => 'VIP'];
                            foreach($brands as $val => $label):
                            ?>
                            <label class="filter-option">
                                <input type="checkbox" name="brand[]" value="<?php echo $val; ?>" <?php echo in_array($val, $selected_brands) ? 'checked' : ''; ?>>
                                <span class="checkmark"></span> <?php echo $label; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>

                        <a href="products.php" class="btn-clear-filters">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </form>
                </aside>

                <!-- Products Grid -->
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding: 0.5rem 1rem; background: #fff; border-radius: 12px; border: 1px solid #eee; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <span style="color: #444; font-weight: 500;">Showing <strong id="productCount" style="color: var(--accent-color);"><?php echo count($filtered_products); ?></strong> matching products</span>
                    </div>
                    <div class="products-grid" id="productGrid">
                    <?php 
                    // Initial Render
                    renderProductsGrid($filtered_products);
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filterForm');
            const checkboxes = form.querySelectorAll('input[type="checkbox"]');
            const grid = document.getElementById('productGrid');
            
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    // Create URL from form data
                    const formData = new FormData(form);
                    const params = new URLSearchParams(formData);
                    
                    // Update Browser URL (History API)
                    const newUrl = window.location.pathname + '?' + params.toString();
                    window.history.pushState({}, '', newUrl);
                    
                    // Add AJAX flag for the fetch request
                    params.append('ajax', '1');
                    
                    // Fetch filtered results
                    fetch('products.php?' + params.toString())
                        .then(response => response.text())
                        .then(html => {
                            grid.innerHTML = html;
                            // Update count 
                            const count = grid.querySelectorAll('.product-card').length;
                            document.getElementById('productCount').textContent = count;
                            document.getElementById('headerCount').textContent = `(${count})`;
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
<?php include '../includes/footer.php'; ?>