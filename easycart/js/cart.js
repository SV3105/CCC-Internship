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
            updateSummary();
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
                updateSummary();
                
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


function updateSummary() {
    let subtotal = 0;
    const items = document.querySelectorAll('.cart-item');
    
    items.forEach(item => {
        const price = parseFloat(item.dataset.price);
        const qty = parseInt(item.querySelector('.qty-input').value);
        subtotal += price * qty;
    });

    // Update Shipping Options UI availability
    const freeOption = document.querySelector('input[name="shipping_method"][value="0"]');
    const freeLabel = document.getElementById('label-free');
    
    if (freeLabel) {
        const freeDesc = freeLabel.querySelector('.shipping-option-desc');
        if (subtotal >= 500) {
            if (freeOption) freeOption.disabled = false;
            if (freeDesc) freeDesc.textContent = 'Eligible for free shipping';
            freeLabel.classList.remove('unavailable');
        } else {
            if (freeOption && freeOption.checked) {
                const normalOption = document.querySelector('input[name="shipping_method"][value="50"]');
                if (normalOption) normalOption.checked = true;
            }
            if (freeOption) freeOption.disabled = true;
            if (freeDesc) freeDesc.textContent = 'Spend ₹' + (500 - subtotal) + ' more for free shipping';
            freeLabel.classList.add('unavailable');
        }
    }

    // Get selected shipping
    const selectedShipping = document.querySelector('input[name="shipping_method"]:checked');
    const shipping = selectedShipping ? parseInt(selectedShipping.value) : 0;
    
    // Update selected class on labels
    document.querySelectorAll('.shipping-option').forEach(label => {
        label.classList.remove('selected');
        const input = label.querySelector('input');
        if (input && input.checked) {
            label.classList.add('selected');
        }
    });

    const tax = subtotal * 0.18;
    const total = subtotal + tax + shipping;

    // Update Summary UI
    const subtotalElem = document.getElementById('summary-subtotal');
    if (subtotalElem) subtotalElem.textContent = '₹' + new Intl.NumberFormat('en-IN').format(Math.round(subtotal));
    
    const shippingElem = document.getElementById('summary-shipping');
    if (shippingElem) {
        shippingElem.textContent = (shipping === 0) ? 'Free' : '₹' + shipping;
        if (shipping === 0) {
            shippingElem.style.color = '#166534';
            shippingElem.style.fontWeight = '700';
        } else {
            shippingElem.style.color = 'inherit';
            shippingElem.style.fontWeight = 'inherit';
        }
    }
    
    const taxElem = document.getElementById('summary-tax');
    if (taxElem) taxElem.textContent = '₹' + new Intl.NumberFormat('en-IN').format(Math.round(tax));
    
    const totalElem = document.getElementById('summary-total');
    if (totalElem) totalElem.textContent = '₹' + new Intl.NumberFormat('en-IN').format(Math.round(total));
    
    // Update Checkout Button Text
    const checkoutBtn = document.getElementById('checkout-btn-text');
    if (checkoutBtn) {
        checkoutBtn.textContent = 'Place Order (₹' + new Intl.NumberFormat('en-IN').format(Math.round(total)) + ')';
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
