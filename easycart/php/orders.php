<?php
$title = "My Orders - EasyCart";
$base_path = "../";
$page = "orders";
$extra_css = "orders.css";
include '../data/products_data.php';
include '../data/orders_data.php';

include '../includes/header.php';
?>

    <div class="container">
        <div class="page-content">
            <h1>My Orders</h1>
            <div class="orders-list">
                <?php foreach($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <p><i class="far fa-calendar-alt"></i> Placed on <?php echo $order['date']; ?></p>
                        </div>
                        <span class="status-badge status-<?php echo $order['status_code']; ?>">
                            <i class="fas <?php echo ($order['status_code'] === 'delivered') ? 'fa-check-circle' : (($order['status_code'] === 'transit') ? 'fa-truck' : 'fa-clock'); ?>"></i>
                            <?php echo $order['status']; ?>
                        </span>
                    </div>
                    
                    <div class="order-body">
                        <!-- Order Status Stepper -->
                        <?php 
                            $current_status = $order['status_code'];
                            $steps = [
                                ['label' => 'Ordered', 'icon' => 'fa-clipboard-check'],
                                ['label' => 'Packed', 'icon' => 'fa-box'],
                                ['label' => 'Shipped', 'icon' => 'fa-shipping-fast'],
                                ['label' => 'Delivered', 'icon' => 'fa-check-circle']
                            ];
                            
                            $progress = 0;
                            $active_index = 0;
                            if ($current_status === 'processing') { $progress = 33; $active_index = 1; }
                            elseif ($current_status === 'transit') { $progress = 66; $active_index = 2; }
                            elseif ($current_status === 'delivered') { $progress = 100; $active_index = 3; }
                        ?>
                        <div class="order-stepper">
                            <div class="stepper-progress" style="width: <?php echo $progress; ?>%;"></div>
                            <?php foreach($steps as $index => $step): ?>
                                <?php 
                                    $class = '';
                                    if ($index < $active_index || ($current_status === 'delivered' && $index <= $active_index)) $class = 'completed';
                                    elseif ($index === $active_index && $current_status !== 'delivered') $class = 'active';
                                ?>
                                <div class="stepper-item <?php echo $class; ?>">
                                    <div class="step-dot">
                                        <i class="fas <?php echo $step['icon']; ?>"></i>
                                    </div>
                                    <span class="step-text"><?php echo $step['label']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="order-items-list">
                            <?php foreach($order['items'] as $item): ?>
                                <div class="order-item">
                                    <i class="fas fa-shopping-bag"></i>
                                    <span class="item-name"><?php echo $item['title']; ?> (x<?php echo $item['qty']; ?>)</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="order-footer">
                        <?php 
                            $order_total = 0;
                            foreach($order['items'] as $item) {
                                $price = (float)str_replace(',', '', $item['price']);
                                $order_total += $price * $item['qty'];
                            }
                            $tax = $order_total * 0.18;
                            $grand_total = $order_total + $tax;
                        ?>
                        <div>
                            <span class="order-total-label">Total Amount:</span>
                            <span class="order-total-amount">₹<?php echo number_format($grand_total); ?></span>
                        </div>
                        <div class="order-actions">
                            <a href="invoice.php?id=<?php echo $order['id']; ?>" target="_blank" class="btn-order-action btn-order-help" style="text-decoration: none;">
                                <i class="fas fa-file-invoice"></i> Download Invoice
                            </a>
                            <?php 
                                // Find product IDs for "Buy Again"
                                $item_ids = [];
                                foreach($order['items'] as $order_item) {
                                    foreach($products as $p) {
                                        if ($p['title'] === $order_item['title']) {
                                            $item_ids[] = $p['id'];
                                            break;
                                        }
                                    }
                                }
                            ?>
                            <button class="btn-order-action btn-buy-again" onclick='buyAgain(<?php echo json_encode($item_ids); ?>)'>
                                <i class="fas fa-redo"></i> Buy Again
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="../js/orders.js"></script>
<?php include '../includes/footer.php'; ?>