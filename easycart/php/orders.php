<?php
$title = "My Orders - EasyCart";
$base_path = "../";
$page = "orders";
$extra_css = "orders.css";
include '../includes/products_data.php';
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
                            <p>Placed on: <?php echo $order['date']; ?></p>
                        </div>
                        <div>
                             <span class="status-badge status-<?php echo $order['status_code']; ?>"><?php echo $order['status']; ?></span>
                        </div>
                    </div>
                    <p>Items: <?php echo implode(', ', $order['items']); ?></p>
                    <p><strong>Total: ₹<?php echo $order['total']; ?></strong></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>