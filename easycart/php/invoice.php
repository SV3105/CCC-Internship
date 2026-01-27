<?php
include '../data/products_data.php';
include '../data/orders_data.php';

$order_id = isset($_GET['id']) ? $_GET['id'] : null;
$order = null;

if ($order_id) {
    foreach ($orders as $o) {
        if ($o['id'] == $order_id) {
            $order = $o;
            break;
        }
    }
}

if (!$order) {
    die("Order not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $order['id']; ?> - EasyCart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/invoice.css">
</head>
<body class="invoice-body">
    <div class="invoice-container">
        <header class="invoice-header">
            <div class="invoice-brand">
                <h1>EasyCart</h1>
                <p>Premium Shopping Experience</p>
                <p style="color: #64748b; font-size: 0.85rem;">Gujarat, India | support@easycart.in</p>
            </div>
            <div class="invoice-details">
                <h2>INVOICE</h2>
                <p><strong>#<?php echo $order['id']; ?></strong></p>
                <p><?php echo $order['date']; ?></p>
            </div>
        </header>

        <section class="invoice-meta">
            <div class="meta-group">
                <h4>Billed To</h4>
                <p>Sneha Vaghela</p>
                <p style="font-weight: 400; color: #64748b; font-size: 0.9rem;">sneha@example.com<br>Ahmedabad, Gujarat</p>
            </div>
            <div class="meta-group" style="text-align: right;">
                <h4>Payment Method</h4>
                <p>Credit Card (Visa)</p>
                <p style="font-weight: 400; color: #64748b; font-size: 0.9rem;">Status: Paid</p>
            </div>
        </section>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal = 0;
                foreach($order['items'] as $order_item): 
                    $price = (float)str_replace(',', '', $order_item['price']);
                    $item_total = $price * $order_item['qty'];
                    $subtotal += $item_total;
                ?>
                <tr>
                    <td><?php echo $order_item['title']; ?></td>
                    <td style="text-align: center;"><?php echo $order_item['qty']; ?></td>
                    <td style="text-align: right;">₹<?php echo number_format($price); ?></td>
                    <td style="text-align: right;">₹<?php echo number_format($item_total); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="invoice-summary">
            <div class="summary-table">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>₹<?php echo number_format($subtotal); ?></span>
                </div>
                <div class="summary-row">
                    <span>Tax (GST 18%)</span>
                    <span>₹<?php echo number_format($subtotal * 0.18); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>
                <div class="summary-row total">
                    <span>Grand Total</span>
                    <span>₹<?php echo number_format($subtotal + ($subtotal * 0.18)); ?></span>
                </div>
            </div>
        </div>

        <div class="invoice-actions">
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i> Print Invoice
            </button>
            <p style="margin-top: 1rem; color: #64748b; font-size: 0.85rem;">Thank you for shopping with EasyCart!</p>
        </div>
    </div>
</body>
</html>
