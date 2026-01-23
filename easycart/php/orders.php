<?php
$title = "My Orders - EasyCart";
$base_path = "../";
$page = "orders";
$extra_css = "orders.css";
include '../data/products_data.php';

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
                    <div style="margin-top: 1rem; border-top: 1px solid #eee; padding-top: 1rem;">
                        <button onclick="callOpenModal(<?php echo $order['id']; ?>)" class="btn-text" style="color: var(--accent-color); font-weight: 600; background: none; border: none; cursor: pointer; padding: 0;">Track Order <i class="fas fa-arrow-right" style="font-size: 0.8rem;"></i></button>

                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeOrderModal()">&times;</span>
            <div id="modalBody">
                <!-- Data will be injected here -->
            </div>
        </div>
    </div>

    <script src="../js/orders.js"></script>
    <script>
        const ordersData = <?php echo json_encode($orders); ?>;
        function callOpenModal(id) { openOrderModal(id, ordersData); }

    </script>

<?php include '../includes/footer.php'; ?>