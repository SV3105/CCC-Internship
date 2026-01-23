<?php
session_start();
$title = "Shopping Cart - EasyCart";
$base_path = "../";
$page = "cart";
$extra_css = "cart.css";
include '../includes/products_data.php';

// --- CART LOGIC ---

// 1. Initialize Cart if empty
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
// Removed hardcoded demo data so cart stays empty when items are removed.

// 2. Handle Actions (Update Quantity / Remove)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $p_id = (int)$_POST['product_id'];
    
    if ($_POST['action'] === 'update_qty') {
        $change = (int)$_POST['change']; // +1 or -1
        
        // Initialize if not exists (for 'Add to Cart' from details page)
        if (!isset($_SESSION['cart'][$p_id])) {
            $_SESSION['cart'][$p_id] = 0;
        }
        
        $_SESSION['cart'][$p_id] += $change;
        
        if ($_SESSION['cart'][$p_id] <= 0) {
            unset($_SESSION['cart'][$p_id]);
        }
    } elseif ($_POST['action'] === 'remove') {
        if (isset($_SESSION['cart'][$p_id])) {
            unset($_SESSION['cart'][$p_id]);
        }
    }
    
    // Redirect to avoid form resubmission
    header("Location: cart.php");
    exit;
}

include '../includes/header.php';
?>

    <div class="container">
        <h1 class="page-title">Shopping Cart</h1>
        
        <div class="cart-layout">
            <!-- Cart Items List -->
            <div class="cart-items">
                <?php
                $subtotal = 0;
                $shipping = 0; // Free shipping
                $cart_empty = true;

                if (!empty($_SESSION['cart'])) {
                    $cart_empty = false;
                    foreach($_SESSION['cart'] as $p_id => $qty):
                        // Find product by ID
                        $item = null;
                        foreach($products as $p) {
                            if($p['id'] == $p_id) {
                                $item = $p;
                                break;
                            }
                        }
                        if($item):
                            // Clean price for calc
                            $price_val = (float)str_replace(',', '', $item['price']);
                            $item_total = $price_val * $qty;
                            $subtotal += $item_total;
                ?>
                <div class="cart-item">
                    <div class="item-visual">
                        <img src="../images/<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>" class="item-img">
                    </div>
                    <div class="item-details">
                        <h3><?php echo $item['title']; ?></h3>
                        <p class="item-category"><?php echo ucfirst($item['category']); ?></p>
                        <h4 class="item-price">₹<?php echo $item['price']; ?></h4>
                    </div>
                    <div class="item-actions">
                        <div class="quantity-control">
                            <!-- Decrease Form -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="update_qty">
                                <input type="hidden" name="change" value="-1">
                                <input type="hidden" name="product_id" value="<?php echo $p_id; ?>">
                                <button type="submit" class="qty-btn" aria-label="Decrease quantity"><i class="fas fa-minus"></i></button>
                            </form>
                            
                            <input type="number" value="<?php echo $qty; ?>" min="1" readonly>
                            
                            <!-- Increase Form -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="update_qty">
                                <input type="hidden" name="change" value="1">
                                <input type="hidden" name="product_id" value="<?php echo $p_id; ?>">
                                <button type="submit" class="qty-btn" aria-label="Increase quantity"><i class="fas fa-plus"></i></button>
                            </form>
                        </div>
                        <p class="item-total">₹<?php echo number_format($item_total); ?></p>
                        
                        <!-- Remove Form -->
                        <form method="POST" style="margin-top: 5px;">
                             <input type="hidden" name="action" value="remove">
                             <input type="hidden" name="product_id" value="<?php echo $p_id; ?>">
                             <button type="submit" class="btn-text text-danger" style="font-size: 0.8rem; background:none; border:none; color: #ef4444; cursor:pointer;">Remove</button>
                        </form>
                    </div>
                </div>
                <?php endif; endforeach; 
                } // End if not empty
                
                if ($cart_empty): ?>
                    <div class="no-results" style="display:block; text-align:center; padding: 2rem;">
                         <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ddd; margin-bottom: 1rem;"></i>
                         <p>Your cart is empty.</p>
                         <a href="products.php" class="btn" style="margin-top: 1rem;">Start Shopping</a>
                    </div>
                <?php else: ?>
                    <!-- Add More Items Button (Left Side) -->
                    <a href="products.php" class="btn-add-more">
                        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Add More Items
                    </a>
                <?php endif; 

                $tax = $subtotal * 0.18; // 18% Tax
                $total = $subtotal + $tax + $shipping;
                ?>
            </div>
            
            <?php if (!$cart_empty): ?>
            <!-- Cart Summary -->
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span>₹<?php echo number_format($subtotal); ?></span>
                </div>
                <div class="summary-item">
                    <span>Shipping</span>
                    <span><?php echo $shipping == 0 ? 'Free' : '₹'.number_format($shipping); ?></span>
                </div>
                <div class="summary-item">
                    <span>Tax (18%)</span>
                    <span>₹<?php echo number_format($tax); ?></span>
                </div>
                <hr>
                <div class="summary-total">
                    <span>Total</span>
                    <span>₹<?php echo number_format($total); ?></span>
                </div>
                
                <a href="#checkout-modal" class="checkout-btn" style="text-align: center; text-decoration: none;">Proceed to Checkout</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Checkout Modal (Pure CSS Target) -->
    <div id="checkout-modal" class="modal-overlay">
        <a href="#" class="modal-close-area"></a>
        <div class="modal-content">
            <div class="modal-header">
                <h2>Checkout</h2>
                <a href="#" class="close-btn">&times;</a>
            </div>
            <div class="modal-body">
                <form action="#" class="checkout-form">
                    <div class="form-section">
                        <h4>Contact Info</h4>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" placeholder="john@example.com" required>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h4>Shipping Address</h4>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" placeholder="123 Street Name" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" placeholder="City" required>
                            </div>
                            <div class="form-group">
                                <label>Postal Code</label>
                                <input type="text" placeholder="000000" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Payment</h4>
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" placeholder="0000 0000 0000 0000" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry</label>
                                <input type="text" placeholder="MM/YY" required>
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" placeholder="123" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-block">Place Order (Rs. <?php echo number_format($total, 2); ?>)</button>
                </form>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>