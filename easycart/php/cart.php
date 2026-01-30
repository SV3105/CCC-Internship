<?php
session_start();
$title = "Shopping Cart - EasyCart";
$base_path = "../";
$page = "cart";
$extra_css = "cart.css";
include '../data/products_data.php';


// --- CART LOGIC ---

// 1. Initialize Cart if empty
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['applied_promo'])) {
    $_SESSION['applied_promo'] = null;
}

/**
 * Centralized Cart Calculation Logic
 * Calculates Subtotal, Shipping, Tax, and Total based on current cart state.
 */
function calculateCartTotals($cart_items, $products_data) {
    $subtotal = 0;
    $total_mrp = 0;
    $total_discount = 0;
    
    // Calculate Subtotal
    foreach($cart_items as $p_id => $qty) {
        foreach($products_data as $p) {
            if($p['id'] == $p_id) {
                // Remove commas from price string (e.g. "1,200" -> 1200)
                $price_val = (float)str_replace(',', '', $p['price']);
                $subtotal += $price_val * $qty;
                
                // MRP Calculation
                $mrp_val = $price_val; // Default to selling price if no old price
                if(isset($p['old_price']) && !empty($p['old_price'])) {
                    $mrp_val = (float)str_replace(',', '', $p['old_price']);
                }
                $total_mrp += $mrp_val * $qty;
                break;
            }
        }
    }
    $total_discount = $total_mrp - $subtotal;

    // --- Shipping Rules (Moved Before Discount) ---
    $shipping_options = [
        'standard' => 40,
        'express' => min(80, $subtotal * 0.10),
        'white_glove' => min(150, $subtotal * 0.05),
        'freight' => max(250, $subtotal * 0.03)
    ];

    // Determine Selected Shipping Method with Smart Validation
    $selected_method = isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : null;

    if ($selected_method === null) {
        // No method set: Default to Express (Small) or Freight (Large)
        $selected_method = ($subtotal <= 300) ? 'express' : 'freight';
        $_SESSION['shipping_method'] = $selected_method;
    } else {
        // Method set: Validate against rules
        if ($subtotal <= 300) {
            // Small Order (< 300)
            // Rule: Express is cheaper/faster. Auto-upgrade Standard -> Express.
            if ($selected_method !== 'express') {
                 $selected_method = 'express';
                 $_SESSION['shipping_method'] = $selected_method;
            }
        } else {
            // Large Order (> 300)
            // Rule: Must be Freight or White Glove.
            if ($selected_method !== 'white_glove' && $selected_method !== 'freight') {
                $selected_method = 'freight';
                $_SESSION['shipping_method'] = $selected_method;
            }
        }
    }

    $shipping_cost = isset($shipping_options[$selected_method]) ? $shipping_options[$selected_method] : 40;
    
    // Empty cart checks
    if (empty($cart_items)) {
        $shipping_cost = 0;
        $shipping_options = array_map(function() { return 0; }, $shipping_options);
    }

    // --- Smart Discount Logic (Quantity Based) ---
    // Rule: 1 Product = 1%, 2 Products = 2%, etc.
    // Calculated on Price (Subtotal) ONLY, excluding shipping.
    $smart_discount = 0;
    $reason = "";
    $item_count = array_sum($cart_items);
    
    // Basis is now just the product price (Subtotal)
    // $cart_total_basis = $subtotal + $shipping_cost; // REMOVED

    if ($item_count > 0) {
        // Cap at 100% to prevent negative total
        $discount_percent = min($item_count, 100); 
        $smart_discount = $subtotal * ($discount_percent / 100);
        $reason = "Quantity Discount ({$discount_percent}% off on items)";
    }

    // --- Promo Code Logic (SaveX -> X% off on Subtotal + Shipping) ---
    $promo_discount = 0;
    $promo_code = isset($_SESSION['applied_promo']) ? $_SESSION['applied_promo'] : null;
    $promo_message = "";

    if ($promo_code) {
        // Strict Validation: Case-Sensitive & Specific Codes Only
        $allowed_codes = ['SAVE5', 'SAVE10', 'SAVE15', 'SAVE20'];
        
        if (in_array($promo_code, $allowed_codes)) {
             // Extract percentage from the valid code (e.g. SAVE5 -> 5)
             $percent = (int)substr($promo_code, 4);
             
             $base_for_promo = $subtotal + $shipping_cost;
             $promo_discount = $base_for_promo * ($percent / 100);
             $promo_message = "{$promo_code} Applied ({$percent}% off)";
             
             // User Request: If promo code is applied, remove smart discount
             if ($promo_discount > 0) {
                 $smart_discount = 0;
                 $reason = ""; 
             }
        }
    }

    // Tax (18%)
    $tax = ($subtotal - $smart_discount + $shipping_cost) * 0.18;

    
    // Total calculation
    $total = ($subtotal - $smart_discount) + $shipping_cost + $tax - $promo_discount;
    $total = max(0, $total); // Ensure no negative total

    return [
        'subtotal' => $subtotal,
        'shipping_cost' => $shipping_cost,
        'tax' => $tax,
        'total' => $total,
        'shipping_options' => $shipping_options,
        'item_count' => $item_count,
        'total_mrp' => $total_mrp,
        'total_discount' => $total_discount,
        'smart_discount' => $smart_discount,
        'discount_reason' => $reason,

        'selected_method' => $selected_method, // Return the validated method
        'promo_discount' => $promo_discount,
        'promo_code' => $promo_code,
        'promo_message' => $promo_message
    ];
}

// 2. Handle Actions (Update Quantity / Remove)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $p_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if ($_POST['action'] === 'update_qty' && $p_id > 0) {
        if (isset($_POST['qty'])) {
            $new_qty = (int)$_POST['qty'];
            $_SESSION['cart'][$p_id] = $new_qty;
        } elseif (isset($_POST['change'])) {
            $change = (int)$_POST['change'];
            if (!isset($_SESSION['cart'][$p_id])) {
                $_SESSION['cart'][$p_id] = 0;
            }
            $_SESSION['cart'][$p_id] += $change;
        }
        
        if (isset($_SESSION['cart'][$p_id]) && $_SESSION['cart'][$p_id] <= 0) {
            unset($_SESSION['cart'][$p_id]);
            // If cart becomes empty, reset shipping preference
            if (empty($_SESSION['cart'])) {
                unset($_SESSION['shipping_method']);
            }
        }
    } elseif ($_POST['action'] === 'remove' && $p_id > 0) {
        if (isset($_SESSION['cart'][$p_id])) {
            unset($_SESSION['cart'][$p_id]);
            // If cart becomes empty, reset shipping preference
            if (empty($_SESSION['cart'])) {
                unset($_SESSION['shipping_method']);
            }
        }
    } elseif ($_POST['action'] === 'set_shipping' && isset($_POST['method'])) {
        $_SESSION['shipping_method'] = $_POST['method'];
    } elseif ($_POST['action'] === 'clear') {
        $_SESSION['cart'] = [];
        unset($_SESSION['shipping_method']); // Reset shipping preference
        unset($_SESSION['applied_promo']);
    } elseif ($_POST['action'] === 'apply_promo' && isset($_POST['code'])) {
        $code = trim($_POST['code']);
    
        $is_valid = false;
        // Strict Validation: Case-Sensitive & Specific Codes Only
        $allowed_codes = ['SAVE5', 'SAVE10', 'SAVE15', 'SAVE20'];
        
        if (in_array($code, $allowed_codes)) {
            $is_valid = true;
        }
        
        if ($is_valid) {
            $_SESSION['applied_promo'] = $code;
        } else {
            // If user types invalid code, we probably shouldn't clear an existing valid one unless explicitly asked.
            // But for this simple flow, applying a new code replaces the old one.
             $_SESSION['applied_promo'] = null; // Clear if invalid
        }
    }



    /*
     * WHY WE USE AJAX VS PAGE RELOAD?
     * -------------------------------------------------------------------------
     * Feature      | Without AJAX (Old School)         | With AJAX (Modern)
     * -------------------------------------------------------------------------
     * Visuals      | Screen flashes white on update    | Smooth, instant updates
     * Speed        | Slow (re-downloads CSS/Images)    | Fast (only sends JSON)
     * Scroll       | Jumps to top after every click    | Stays exactly where you are
     * -------------------------------------------------------------------------
     * 
     * HOW IT WORKS:
     * 1. Page Load: PHP renders full HTML with initial values.
     * 2. User Action: JS sends hidden background request (AJAX).
     * 3. PHP: Detects this flag, calculates new totals, and sends ONLY JSON.
     * 4. JS: Receives JSON and updates specific numbers in the DOM.
     */

    // Check for AJAX/Fetch request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        
        // Use the centralized function
        $totals = calculateCartTotals($_SESSION['cart'], $products);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'summary' => [
                'subtotal' => $totals['subtotal'],
                'shipping' => $totals['shipping_cost'], // JS expects 'shipping'
                'tax' => $totals['tax'],
                'total' => $totals['total'],
                'total' => $totals['total'],
                'count' => $totals['item_count'],
                'mrp' => $totals['total_mrp'],
                'discount' => $totals['total_discount'],
                'smart_discount' => $totals['smart_discount'],
                'reason' => $totals['discount_reason'],
                'smart_discount' => $totals['smart_discount'],
                'reason' => $totals['discount_reason'],
                'shipping_options' => $totals['shipping_options'],
                'promo_discount' => $totals['promo_discount'],
                'promo_code' => $totals['promo_code'],
                'promo_message' => $totals['promo_message']
            ]
        ]);
        exit;
    }
    
    // Redirect for standard form submissions
    header("Location: cart.php");
    exit;
}

include '../includes/header.php';
?>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 class="page-title" style="margin: 0;">Shopping Cart</h1>
            <?php if (!empty($_SESSION['cart'])): ?>
            <button onclick="clearCart()" class="btn-clear-cart">
                <i class="fas fa-trash-alt"></i> Clear Cart
            </button>
            <?php endif; ?>
        </div>


        
        <div class="cart-layout">
            <!-- Cart Items List -->
            <div class="cart-items">
                <?php
                $subtotal = 0;
                $shipping = 0; 
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
                <div class="cart-item" data-id="<?php echo $p_id; ?>" data-price="<?php echo $price_val; ?>">
                    <div class="item-visual">
                        <img src="../images/<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>" class="item-img">
                    </div>
                    <div class="item-details">
                        <h3><?php echo $item['title']; ?></h3>
                        <p class="item-category"><?php echo ucfirst($item['category']); ?></p>
                        <h4 class="item-price">
                            ₹<?php echo $item['price']; ?>
                            <?php if(isset($item['old_price']) && !empty($item['old_price'])): ?>
                                <span class="cart-old-price">₹<?php echo $item['old_price']; ?></span>
                                <?php 
                                    $p_val = (float)str_replace(',', '', $item['price']);
                                    $o_val = (float)str_replace(',', '', $item['old_price']);
                                    if($o_val > 0) {
                                        $d_pct = round((($o_val - $p_val) / $o_val) * 100);
                                        if($d_pct > 0) echo "<span class='cart-discount-badge'>{$d_pct}% OFF</span>";
                                    }
                                ?>
                            <?php endif; ?>
                        </h4>
                    </div>
                    <div class="item-actions">
                        <div class="quantity-control">
                            <button type="button" class="qty-btn" onclick="updateQty(<?php echo $p_id; ?>, -1)"><i class="fas fa-minus"></i></button>
                            <input type="number" class="qty-input" value="<?php echo $qty; ?>" min="1" readonly>
                            <button type="button" class="qty-btn" onclick="updateQty(<?php echo $p_id; ?>, 1)"><i class="fas fa-plus"></i></button>
                        </div>
                        <p class="item-total">₹<span class="item-subtotal-val"><?php echo number_format($item_total, 2); ?></span></p>
                        
                        <button type="button" class="btn-text text-danger" onclick="removeCartItem(<?php echo $p_id; ?>)" style="font-size: 0.8rem; background:none; border:none; color: #ef4444; cursor:pointer;">Remove</button>
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
                

                
                // --- Phase 4: Shipping & Tax Logic (Rupee Edition) ---
                // REFACTORED: Now using the centralized function
                $cart_totals = calculateCartTotals($_SESSION['cart'], $products);
                
                // Extract variables for use in HTML below
                $subtotal = $cart_totals['subtotal']; // Note: This overwrites the loop subtotal, which is safer
                $shipping_cost = $cart_totals['shipping_cost'];
                $tax = $cart_totals['tax'];
                $tax = $cart_totals['tax'];
                $total = $cart_totals['total'];
                $total_mrp = $cart_totals['total_mrp'];
                $total_discount = $cart_totals['total_discount'];
                $smart_discount = $cart_totals['smart_discount'];
                $reason = $cart_totals['discount_reason'];
                $smart_discount = $cart_totals['smart_discount'];
                $reason = $cart_totals['discount_reason'];
                $shipping_options = $cart_totals['shipping_options'];
                $promo_discount = $cart_totals['promo_discount'];
                $promo_message = $cart_totals['promo_message'];
                $applied_promo_code = $cart_totals['promo_code'];
                // Use the VALIDATED shipping method from calculation logic
                $shipping_method = isset($cart_totals['selected_method']) ? $cart_totals['selected_method'] : (isset($_SESSION['shipping_method']) ? $_SESSION['shipping_method'] : 'standard');

                // --- FINAL SAFETY CHECK (Band-aid for Session Persistence Issues) ---
                // Force the correct method based on subtotal rules, ignoring session if it mismatches
                if ($subtotal > 300) {
                     // Large Order: Must be freight or white_glove
                    if ($shipping_method === 'standard' || $shipping_method === 'express') {
                        $shipping_method = 'freight';
                    }
                } else {
                    // Small Order: Must be standard or express
                    if ($shipping_method === 'freight' || $shipping_method === 'white_glove') {
                        $shipping_method = 'express';
                    }
                }
                ?>
            </div>
            
            <?php if (!$cart_empty): ?>
            <!-- Cart Summary -->
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-item">
                    <span id="summary-price-label">Price (<?php echo $cart_totals['item_count']; ?> items)</span>
                    <span id="summary-subtotal">₹<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <?php if($smart_discount > 0): ?>
                <div class="summary-item" id="row-smart-discount">
                    <span class="smart-label">
                        Smart Discount 
                        <div class="tooltip-container">
                            <i class="fas fa-info-circle info-icon"></i>
                            <span class="tooltip-text" id="tooltip-text">
                                <?php echo !empty($reason) ? $reason : 'Discount Applied'; ?>
                            </span>
                        </div>
                    </span>
                    <span id="summary-smart-discount" style="color: var(--success, #16a34a);">-₹<?php echo number_format($smart_discount, 2); ?></span>
                </div>
                <?php else: ?>
                <div class="summary-item" id="row-smart-discount" style="display:none;">
                    <span class="smart-label">
                        Smart Discount
                        <div class="tooltip-container">
                            <i class="fas fa-info-circle info-icon"></i>
                            <span class="tooltip-text" id="tooltip-text"></span>
                        </div>
                    </span>
                    <span id="summary-smart-discount" style="color: var(--success, #16a34a);">-₹0</span>
                </div>
                <?php endif; ?>

                <?php if($promo_discount > 0): ?>
                <div class="summary-item" id="row-promo-discount">
                    <span class="promo-label" style="color: var(--primary);">
                        <?php echo $promo_message; ?>
                    </span>
                    <span id="summary-promo-discount" style="color: var(--success, #16a34a);">-₹<?php echo number_format($promo_discount, 2); ?></span>
                </div>
                <?php else: ?>
                 <div class="summary-item" id="row-promo-discount" style="display:none;">
                    <span class="promo-label" style="color: var(--primary);">Promo Discount</span>
                    <span id="summary-promo-discount" style="color: var(--success, #16a34a);">-₹0.00</span>
                </div>
                <?php endif; ?>

                <div class="summary-item">
                    <span>Shipping</span>
                    <span id="summary-shipping">₹<?php echo number_format($shipping_cost, 2); ?></span>
                </div>
                <div class="summary-item">
                    <span>Tax (18%)</span>
                    <span id="summary-tax">₹<?php echo number_format($tax, 2); ?></span>
                </div>
                
                <div class="shipping-methods">
                    <div class="shipping-header" onclick="toggleShipping()">
                        <h4>Shipping Method</h4>
                        <i class="fas fa-chevron-down" id="shipping-chevron"></i>
                    </div>
                    <div class="shipping-options" id="shipping-options-container" style="display: none;">
                        <!-- Standard Shipping (Only for <= 300) -->
                        <?php $standard_disabled = ($subtotal > 300); ?>
                        <label class="shipping-option <?php echo $standard_disabled ? 'disabled-option' : ''; ?>"
                               style="<?php echo $standard_disabled ? 'opacity: 0.5; pointer-events: none;' : ''; ?>">
                            <input type="radio" name="shipping_method" value="standard" 
                                <?php echo ($shipping_method === 'standard') ? 'checked' : ''; ?> 
                                <?php echo $standard_disabled ? 'disabled' : ''; ?>
                                onchange="updateShipping('standard')">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">Standard Shipping</span>
                                <span class="shipping-option-desc">Flat Rate Delivery</span>
                            </div>
                            <span class="shipping-option-price">₹40</span>
                        </label>
                        
                        <!-- Express Shipping (Only for <= 300) -->
                        <?php $express_disabled = ($subtotal > 300); ?>
                        <label class="shipping-option <?php echo $express_disabled ? 'disabled-option' : ''; ?>" 
                               style="<?php echo $express_disabled ? 'opacity: 0.5; pointer-events: none;' : ''; ?>">
                            <input type="radio" name="shipping_method" value="express" 
                                <?php echo ($shipping_method === 'express') ? 'checked' : ''; ?> 
                                <?php echo $express_disabled ? 'disabled' : ''; ?>
                                onchange="updateShipping('express')">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">Express Shipping</span>
                                <span class="shipping-option-desc">₹80 or 10% (Lowest)</span>
                            </div>
                            <span class="shipping-option-price">₹<?php echo number_format($shipping_options['express'], 2); ?></span>
                        </label>
                        
                        <!-- White Glove Delivery (Only for > 300) -->
                        <?php $white_glove_disabled = ($subtotal <= 300); ?>
                        <label class="shipping-option <?php echo $white_glove_disabled ? 'disabled-option' : ''; ?>"
                               style="<?php echo $white_glove_disabled ? 'opacity: 0.5; pointer-events: none;' : ''; ?>">
                            <input type="radio" name="shipping_method" value="white_glove" 
                                <?php echo ($shipping_method === 'white_glove') ? 'checked' : ''; ?> 
                                <?php echo $white_glove_disabled ? 'disabled' : ''; ?>
                                onchange="updateShipping('white_glove')">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">White Glove Delivery</span>
                                <span class="shipping-option-desc">₹150 or 5% (Lowest)</span>
                            </div>
                            <span class="shipping-option-price">₹<?php echo number_format($shipping_options['white_glove'], 2); ?></span>
                        </label>

                        <!-- Freight Shipping (Only for > 300) -->
                        <?php $freight_disabled = ($subtotal <= 300); ?>
                        <label class="shipping-option <?php echo $freight_disabled ? 'disabled-option' : ''; ?>"
                               style="<?php echo $freight_disabled ? 'opacity: 0.5; pointer-events: none;' : ''; ?>">
                            <input type="radio" name="shipping_method" value="freight" 
                                <?php echo ($shipping_method === 'freight') ? 'checked' : ''; ?> 
                                <?php echo $freight_disabled ? 'disabled' : ''; ?>
                                onchange="updateShipping('freight')">
                            <div class="shipping-option-info">
                                <span class="shipping-option-name">Freight Shipping</span>
                                <span class="shipping-option-desc">3% or Min ₹250</span>
                            </div>
                            <span class="shipping-option-price">₹<?php echo number_format($shipping_options['freight'], 2); ?></span>
                        </label>
                    </div>
                </div>

                <div class="promo-code-section">
                    <label style="font-size: 0.9rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; display: block;">Promo Code</label>
                    <div class="promo-input-group">
                        <input type="text" id="promo-code" placeholder="Enter promo code">
                        <button type="button" class="btn-apply" onclick="applyPromo()">Apply</button>
                    </div>
                    <div id="promo-message" style="display:none; font-size: 0.85rem; margin-top: 0.5rem;"></div>
                </div>

                <hr>
                <div class="summary-total">
                    <span>Total</span>
                    <span id="summary-total">₹<?php echo number_format($total, 2); ?></span>
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
                <form action="#" class="checkout-form" id="checkoutForm">
                    <div class="form-section">
                        <h4>Contact Info</h4>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" id="checkoutName" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" id="checkoutEmail" placeholder="john@example.com" required>
                        </div>
                    </div>
                    
                    <div class="form-section address-highlight">
                        <h4>Shipping Address</h4>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" id="checkoutAddress" placeholder="123 Street Name" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" id="checkoutCity" placeholder="City" required>
                            </div>
                            <div class="form-group">
                                <label>Postal Code</label>
                                <input type="text" id="checkoutZip" placeholder="000000" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Payment</h4>
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" id="checkoutCard" placeholder="0000 0000 0000 0000" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry</label>
                                <input type="text" id="checkoutExpiry" placeholder="MM/YY" required>
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" id="checkoutCVV" placeholder="123" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-block" id="checkout-btn-text">Place Order (₹<?php echo number_format($total, 2); ?>)</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/cart.js?v=<?php echo time(); ?>"></script>


<?php include '../includes/footer.php'; ?>