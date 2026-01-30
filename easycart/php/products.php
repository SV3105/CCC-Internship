<?php
session_start();

$title = "Shop - EasyCart India";
$base_path = "../";
$page = "products";
$extra_css = "products.css";
include '../data/products_data.php';

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
        $search_words = explode(' ', strtolower($search_query));

        
        // Load category synonyms for matching (generalized names only)
        $cat_labels = [
            'electronics' => 'Tech Electronics Technology Gadgets',
            'fashion' => 'Fashion Clothing Apparel Wear',
            'home' => 'Home Travel Luggage Living',
            'beauty' => 'Beauty Personal Care Health'
        ];
        
        foreach ($search_words as $word) {
            $word = trim($word);
            if ($word === '') continue;
            
            $word_found = false;
            
            // 1. Check in Title
            if (stripos($product['title'], $word) !== false) $word_found = true;
            // 2. Check in Brand
            elseif (stripos($product['brand'], $word) !== false) $word_found = true;
            // 3. Check in Category Slug OR Display Label
            elseif (stripos($product['category'], $word) !== false) $word_found = true;
            elseif (isset($cat_labels[$product['category']]) && stripos($cat_labels[$product['category']], $word) !== false) $word_found = true;
            // 4. Check in Description
            elseif (isset($product['description']) && stripos($product['description'], $word) !== false) $word_found = true;
            // 5. Check in Image Filename (removes .png/.jpg etc)
            elseif (stripos($product['image'], $word) !== false) $word_found = true;
            // 6. Check in Features
            elseif (isset($product['features']) && is_array($product['features'])) {
                foreach ($product['features'] as $feature) {
                    if (stripos($feature, $word) !== false) {
                        $word_found = true;
                        break;
                    }
                }
            }
            
            // If THIS word was not found in ANY field, then the product is not a match
            if (!$word_found) {
                $search_match = false;
                break;
            }
        }
    }



    
    return $cat_match && $brand_match && $search_match;
});

// --- PAGINATION LOGIC ---
$limit = 16; // Products per page
$page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page_num < 1) $page_num = 1;

$total_products = count($filtered_products);
$total_pages = ceil($total_products / $limit);

// Ensure page doesn't exceed total pages
if ($page_num > $total_pages && $total_pages > 0) $page_num = $total_pages;

$offset = ($page_num - 1) * $limit;
$paginated_products = array_slice($filtered_products, $offset, $limit);

// 3. Render Function (HTML Output for Grid Items + Pagination)
function renderProductsGrid($items, $page, $total_pages) {
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    
    if(!empty($items)):
        foreach($items as $product): 
            $qty = isset($cart[$product['id']]) ? (int)$cart[$product['id']] : 0;
            
            // Calculate Discount
            $discount_percent = 0;
            if($product['old_price']) {
                $p_val = floatval(str_replace(',', '', $product['price']));
                $o_val = floatval(str_replace(',', '', $product['old_price']));
                if($o_val > 0) {
                    $discount_percent = round((($o_val - $p_val) / $o_val) * 100);
                }
            }
            ?>
    <div class="product-card" data-id="<?php echo $product['id']; ?>">
        <?php 
        $in_wishlist = isset($_SESSION['wishlist']) && in_array($product['id'], $_SESSION['wishlist']);
        ?>
        <button class="btn-wishlist-toggle" onclick="toggleWishlist(<?php echo $product['id']; ?>, this)">
            <i class="<?php echo $in_wishlist ? 'fas active-wishlist' : 'far'; ?> fa-heart"></i>
        </button>
        <div class="product-image-container">
            <?php if($discount_percent > 0): ?>
                <span class="discount-badge"><?php echo $discount_percent; ?>% OFF</span>
            <?php endif; ?>
            <img src="../images/<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>">
        </div>
        <h3><?php echo $product['title']; ?></h3>
        <p class="price">₹<?php echo $product['price']; ?>
            <?php if($product['old_price']): ?>
                <span class="old-price">₹<?php echo $product['old_price']; ?></span>
            <?php endif; ?>
        </p>
        
        <div class="quick-add-container">
            <?php if ($qty > 0): ?>
                <div class="qty-selector">
                    <button class="btn-qty btn-minus" onclick="updateQuickQty(<?php echo $product['id']; ?>, -1)">-</button>
                    <span class="qty-display"><?php echo $qty; ?></span>
                    <button class="btn-qty btn-plus" onclick="updateQuickQty(<?php echo $product['id']; ?>, 1)">+</button>
                </div>
            <?php else: ?>
                <button class="btn btn-quick-add" onclick="updateQuickQty(<?php echo $product['id']; ?>, 1)">
                    <i class="fas fa-plus"></i> Add to Cart
                </button>
            <?php endif; ?>
        </div>

        <a href="./<?php echo $product['url']; ?>" class="btn-view-details">View Details <i class="fas fa-chevron-right"></i></a>

    </div>
    <?php endforeach; 
    else: ?>
    <div class="no-results" style="display:block;">
        <i class="fas fa-search"></i>
        <p>No products found matching your filters.</p>
    </div>
    <?php endif; 

    // Render Pagination UI
    if ($total_pages > 1): ?>
    <div class="pagination">
        <!-- Prev Button -->
        <?php if ($page > 1): ?>
            <button onclick="changePage(<?php echo $page - 1; ?>)" class="btn-page"><i class="fas fa-chevron-left"></i></button>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <button onclick="changePage(<?php echo $i; ?>)" class="btn-page <?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </button>
        <?php endfor; ?>

        <!-- Next Button -->
        <?php if ($page < $total_pages): ?>
            <button onclick="changePage(<?php echo $page + 1; ?>)" class="btn-page"><i class="fas fa-chevron-right"></i></button>
        <?php endif; ?>
    </div>
    <?php endif;
}

// 4. AJAX HANDLER
// If this is an AJAX request, ONLY output the grid and exit.
// If this is an AJAX request, ONLY output the grid and exit.
if(isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    renderProductsGrid($paginated_products, $page_num, $total_pages);
    exit;
}

// --- NORMAL PAGE LOAD ---
include '../includes/header.php';
?>


<link rel="stylesheet" href="../css/wishlist.css">

    <div class="container">
        <div class="page-content layout-transparent">
            <h1 class="text-dark mb-4">Explore Collection <span id="headerCount" style="font-size: 1.5rem; color: #888; font-weight: 400;">(<?php echo count($filtered_products); ?>)</span></h1>
            
            <div class="shop-layout">
                <!-- Filters -->
                <aside class="filter-sidebar">
                    <form action="" method="GET" id="filterForm">
                        <!-- Preserve Search Query -->
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                        <!-- Hidden Page Input for Pagination -->
                        <input type="hidden" name="page" id="pageInput" value="<?php echo $page_num; ?>">

                        <div class="filter-group">
                            <h3>Categories</h3>
                            <?php 
                            // Use centralized $categories from products_data.php
                            foreach($categories as $val => $data):
                            ?>
                            <label class="filter-option">
                                <input type="checkbox" name="category[]" value="<?php echo $val; ?>" <?php echo in_array($val, $selected_categories) ? 'checked' : ''; ?>>
                                <span class="checkmark"></span> <?php echo $data['name']; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    
                        <div class="filter-group">
                            <h3>Brands</h3>
                            <?php 
                            // Use centralized $brands from products_data.php
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
                    renderProductsGrid($paginated_products, $page_num, $total_pages);
                    ?>
                </div>
            </div>
        </div>
    </div>
    </div>
    
    <script src="../js/products.js?v=<?php echo time(); ?>"></script>

<?php include '../includes/footer.php'; ?>