<?php
session_start();
$title = "My Wishlist - EasyCart";
$base_path = "../";
$page = "wishlist";
$extra_css = "wishlist.css";
include '../data/products_data.php';

// --- WISHLIST LOGIC ---
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $p_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if ($p_id > 0) {
        if ($_POST['action'] === 'add') {
            if (!in_array($p_id, $_SESSION['wishlist'])) {
                $_SESSION['wishlist'][] = $p_id;
            }
        } elseif ($_POST['action'] === 'remove') {
            if (($key = array_search($p_id, $_SESSION['wishlist'])) !== false) {
                unset($_SESSION['wishlist'][$key]);
                $_SESSION['wishlist'] = array_values($_SESSION['wishlist']); // Re-index
            }
        }
    }

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    
    header("Location: wishlist.php");
    exit;
}

include '../includes/header.php';
?>

<div class="container">
    <div class="page-content">
        <h1 class="page-title">My Wishlist</h1>
        
        <div class="wishlist-grid">
            <?php
            $wishlist_empty = true;
            if (!empty($_SESSION['wishlist'])) {
                foreach($_SESSION['wishlist'] as $p_id):
                    $item = null;
                    foreach($products as $p) {
                        if($p['id'] == $p_id) {
                            $item = $p;
                            break;
                        }
                    }
                    if($item):
                        $wishlist_empty = false;
            ?>
            <div class="product-card wishlist-card" data-id="<?php echo $p_id; ?>">
                <button class="btn-wishlist-remove" onclick="toggleWishlist(<?php echo $p_id; ?>, this)">
                    <i class="fas fa-times"></i>
                </button>
                <div class="product-image-container">
                    <img src="../images/<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>">
                </div>
                <h3><?php echo $item['title']; ?></h3>
                <p class="price">₹<?php echo $item['price']; ?></p>
                
                <div class="wishlist-actions">
                    <a href="product-details.php?id=<?php echo $p_id; ?>" class="btn-view-details-pill">View Details</a>
                    <div class="quick-add-container">
                        <button class="btn btn-quick-add" onclick="updateQuickQty(<?php echo $p_id; ?>, 1)">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; endforeach; } ?>
            
            <?php if ($wishlist_empty): ?>
                <div class="no-results" style="grid-column: 1/-1; display:block; text-align:center; padding: 4rem 0;">
                    <i class="far fa-heart" style="font-size: 4rem; color: #ddd; margin-bottom: 1.5rem;"></i>
                    <p>Your wishlist is empty.</p>
                    <a href="products.php" class="btn" style="margin-top: 1.5rem;">Discover Products</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="../js/wishlist.js"></script>
<script src="../js/products.js"></script>

<?php include '../includes/footer.php'; ?>
