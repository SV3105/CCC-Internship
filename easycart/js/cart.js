// --- Cart Interactions ---
function updateQty(productId, change) {
    const itemRow = document.querySelector(`.cart-item[data-id="${productId}"]`);
    if (!itemRow) return;

    const qtyInput = itemRow.querySelector('.qty-input');
    const price = parseFloat(itemRow.dataset.price);
    let currentQty = parseInt(qtyInput.value);
    
    let newQty = currentQty + change;
    if (newQty < 1) return;

    // Sync with session
    const formData = new FormData();
    formData.append('action', 'update_qty');
    formData.append('product_id', productId);
    formData.append('qty', newQty);

    fetch('cart.php', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            qtyInput.value = newQty;
            // Update item subtotal in UI
            const subtotalSpan = itemRow.querySelector('.item-subtotal-val');
            const newSubtotal = price * newQty;
            subtotalSpan.textContent = new Intl.NumberFormat('en-IN').format(Math.round(newSubtotal));
            updateSummary(data.summary);
        }
    })
    .catch(err => console.error('Error updating quantity:', err));
}

function removeCartItem(productId) {
    if (!confirm('Are you sure you want to remove this item?')) return;
    const itemRow = document.querySelector(`.cart-item[data-id="${productId}"]`);
    if (!itemRow) return;

    // Sync with session
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('product_id', productId);

    fetch('cart.php', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            itemRow.classList.add('removing');
            setTimeout(() => {
                itemRow.remove();
                updateSummary(data.summary);
                
                // If cart is empty now
                const remainingItems = document.querySelectorAll('.cart-item');
                if (remainingItems.length === 0) {
                    location.reload(); 
                }
            }, 400); // Wait for CSS transition
        }
    })
    .catch(err => console.error('Error removing item:', err));
}

function clearCart() {
    if (!confirm('Are you sure you want to empty your entire cart?')) return;

    const formData = new FormData();
    formData.append('action', 'clear');

    fetch('cart.php', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(err => console.error('Error clearing cart:', err));
}


function updateShipping(method) {
    const formData = new FormData();
    formData.append('action', 'set_shipping');
    formData.append('method', method);

    fetch('cart.php', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateSummary(data.summary);
        }
    })
    .catch(err => console.error('Error updating shipping:', err));
}

function updateSummary(summaryData = null) {
    if (!summaryData) {
        console.error('updateSummary called without data from server');
        return;
    }

    // Use data directly from server (Messenger Pattern)
    const subtotal = summaryData.subtotal;
    const shipping = summaryData.shipping;
    const tax = summaryData.tax;
    const total = summaryData.total;
    const cartCount = summaryData.count;
    const shippingOptions = summaryData.shipping_options;

    // Update Shipping Option Labels (Solve the Mismatch)
    if (shippingOptions) {
        Object.keys(shippingOptions).forEach(method => {
            const label = document.querySelector(`input[value="${method}"]`).closest('.shipping-option');
            if (label) {
                const priceSpan = label.querySelector('.shipping-option-price');
                if (priceSpan) {
                    priceSpan.textContent = '₹' + new Intl.NumberFormat('en-IN').format(Math.round(shippingOptions[method]));
                }
            }
        });
    }

    // Update selected class on labels
    document.querySelectorAll('.shipping-option').forEach(label => {
        label.classList.remove('selected');
        const input = label.querySelector('input');
        if (input && input.checked) {
            label.classList.add('selected');
        }
    });

    // Update Summary UI
    const subtotalElem = document.getElementById('summary-subtotal');
    if (subtotalElem) subtotalElem.textContent = '₹' + new Intl.NumberFormat('en-IN').format(Math.round(subtotal));
    
    const shippingElem = document.getElementById('summary-shipping');
    if (shippingElem) shippingElem.textContent = '₹' + new Intl.NumberFormat('en-IN').format(Math.round(shipping));
    
    const taxElem = document.getElementById('summary-tax');
    if (taxElem) taxElem.textContent = '₹' + new Intl.NumberFormat('en-IN').format(Math.round(tax));
    
    const totalElem = document.getElementById('summary-total');
    if (totalElem) totalElem.textContent = '₹' + new Intl.NumberFormat('en-IN').format(Math.round(total));
    
    // Update Checkout Button Text
    const checkoutBtn = document.getElementById('checkout-btn-text');
    if (checkoutBtn) {
        checkoutBtn.textContent = 'Place Order (₹' + new Intl.NumberFormat('en-IN').format(Math.round(total)) + ')';
    }

    // Sync Header Cart Count (Phase 5 Bonus)
    const cartBadge = document.getElementById('cart-count');
    if (cartBadge) {
        cartBadge.textContent = cartCount;
    }
}

// --- Checkout Validation ---
document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkoutForm');

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('checkoutName').value.trim();
            const email = document.getElementById('checkoutEmail').value.trim();
            const address = document.getElementById('checkoutAddress').value.trim();
            const city = document.getElementById('checkoutCity').value.trim();
            const zip = document.getElementById('checkoutZip').value.trim();
            const card = document.getElementById('checkoutCard').value.replace(/\s/g, '');
            const expiry = document.getElementById('checkoutExpiry').value.trim();
            const cvv = document.getElementById('checkoutCVV').value.trim();

            // Validation rules
            if (name.length < 2) {
                alert('Please enter your full name.');
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address.');
                return;
            }

            if (address.length < 5) {
                alert('Please enter a valid shipping address.');
                return;
            }

            if (city.length < 2) {
                alert('Please enter your city.');
                return;
            }

            if (!/^\d{6}$/.test(zip)) {
                alert('Postal code must be 6 digits.');
                return;
            }

            if (!/^\d{16}$/.test(card)) {
                alert('Card number must be 16 digits.');
                return;
            }

            if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
                alert('Expiry must be in MM/YY format.');
                return;
            }

            if (!/^\d{3}$/.test(cvv)) {
                alert('CVV must be 3 digits.');
                return;
            }

            alert('Order placed successfully! Redirecting to orders...');
            window.location.href = 'orders.php';
        });
    }
});
