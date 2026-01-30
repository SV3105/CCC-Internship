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
            subtotalSpan.textContent = new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(newSubtotal);
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

function toggleShipping() {
    const container = document.getElementById('shipping-options-container');
    const chevron = document.getElementById('shipping-chevron');
    
    if (container.style.display === 'none') {
        container.style.display = 'flex';
        chevron.style.transform = 'rotate(180deg)';
    } else {
        container.style.display = 'none';
        chevron.style.transform = 'rotate(0deg)';
    }
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
    // const mrp = summaryData.mrp; // Not used
    // const discount = summaryData.discount; // Not used
    const smartDiscount = summaryData.smart_discount;
    const smartReason = summaryData.reason;
    const promoDiscount = summaryData.promo_discount;
    const promoMessage = summaryData.promo_message;
    const cartCount = summaryData.count;
    const shippingOptions = summaryData.shipping_options;

    // Update Shipping Option Labels (Solve the Mismatch)
    // Update Shipping Option Labels (Solve the Mismatch)
    if (shippingOptions) {
        // Parse subtotal to float (remove commas)
        const currentSubtotal = parseFloat(String(subtotal).replace(/,/g, ''));
        const isSmallOrder = (currentSubtotal <= 300);
        
        // Define availability rules (Refined Grouping)
        const rules = {
            'standard': isSmallOrder,
            'express': isSmallOrder,
            'white_glove': !isSmallOrder,
            'freight': !isSmallOrder
        };

        let currentMethod = document.querySelector('input[name="shipping_method"]:checked')?.value;
        let methodChanged = false;

        Object.keys(shippingOptions).forEach(method => {
            const label = document.querySelector(`input[value="${method}"]`).closest('.shipping-option');
            const input = document.querySelector(`input[value="${method}"]`);
            
            if (label && input) {
                // Update Price Text
                const priceSpan = label.querySelector('.shipping-option-price');
                if (priceSpan) {
                    priceSpan.textContent = '₹' + new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(shippingOptions[method]);
                }

                // Apply Availability Logic
                const isAllowed = rules[method];
                
                if (isAllowed) {
                    input.disabled = false;
                    label.style.opacity = '1';
                    label.style.pointerEvents = 'auto';
                    label.classList.remove('disabled-option');
                } else {
                    input.disabled = true;
                    label.style.opacity = '0.5';
                    label.style.pointerEvents = 'none';
                    label.classList.add('disabled-option');

                    // If currently selected is now disabled, switch to a valid default
                    if (currentMethod === method) {
                        methodChanged = true;
                    }
                }
            }
        });

        // If the selected method is no longer valid, switch to a valid default
        if (methodChanged) {
            let newDefault = isSmallOrder ? 'express' : 'freight';
             const defaultInput = document.querySelector(`input[value="${newDefault}"]`);
             if (defaultInput) {
                 defaultInput.checked = true;
                 updateShipping(newDefault); // Trigger update
             }
        }
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
    const priceLabel = document.getElementById('summary-price-label');
    if (priceLabel) priceLabel.textContent = `Price (${cartCount} items)`;

    // Removed MRP & Discount updates


    const smartRow = document.getElementById('row-smart-discount');
    const smartElem = document.getElementById('summary-smart-discount');
    const tooltipElem = document.getElementById('tooltip-text');

    if (smartRow && smartElem) {
        if (smartDiscount > 0) {
            smartRow.style.display = 'flex';
            smartElem.textContent = '-₹' + new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(smartDiscount);
            if (tooltipElem && smartReason) tooltipElem.textContent = smartReason;
        } else {
            smartRow.style.display = 'none';
        }
    }


    const promoRow = document.getElementById('row-promo-discount');
    const promoElem = document.getElementById('summary-promo-discount');
    
    if (promoRow && promoElem) {
        if (promoDiscount > 0) {
            promoRow.style.display = 'flex';
            promoElem.textContent = '-₹' + new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(promoDiscount);
             const label = promoRow.querySelector('.promo-label');
             if (label && promoMessage) label.textContent = promoMessage;
        } else {
             promoRow.style.display = 'none';
        }
    }

    const subtotalElem = document.getElementById('summary-subtotal');
    if (subtotalElem) subtotalElem.textContent = '₹' + new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(subtotal);
    
    const shippingElem = document.getElementById('summary-shipping');
    if (shippingElem) shippingElem.textContent = '₹' + new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(shipping);
    
    const taxElem = document.getElementById('summary-tax');
    if (taxElem) taxElem.textContent = '₹' + new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(tax);
    
    const totalElem = document.getElementById('summary-total');
    if (totalElem) totalElem.textContent = '₹' + new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(total);
    
    // Update Checkout Button Text
    const checkoutBtn = document.getElementById('checkout-btn-text');
    if (checkoutBtn) {
        checkoutBtn.textContent = 'Place Order (₹' + new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(total) + ')';
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

// --- Promo Code Logic ---
function applyPromo() {
    const codeInput = document.getElementById('promo-code');
    const messageDiv = document.getElementById('promo-message');
    const code = codeInput.value.trim();
    
    // Reset state
    messageDiv.style.display = 'none';
    messageDiv.className = '';
    


    const promoRow = document.getElementById('row-promo-discount');
    const isPromoActive = promoRow && promoRow.style.display !== 'none';

    if (!code) {
        if (!isPromoActive) {
            messageDiv.textContent = 'Please enter a promo code.';
            messageDiv.style.color = '#ef4444';
            messageDiv.style.display = 'block';
            return;
        }
    }

    const formData = new FormData();
    formData.append('action', 'apply_promo');
    formData.append('code', code);

    const btn = document.querySelector('.btn-apply');
    const originalText = btn.textContent;
    btn.textContent = 'Applying...';
    btn.disabled = true;

    fetch('cart.php', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        btn.textContent = originalText;
        btn.disabled = false;

        if (data.success) {
            // Check if promo was actually applied or if it was invalid (implied by promo_discount > 0 or code being set)
            // But our PHP sets session even if valid format but maybe 0 discount? 
            // Let's rely on data.summary.promo_code
            
            if (data.summary.promo_code) {
                messageDiv.textContent = `Promo code "${data.summary.promo_code}" applied!`;
                messageDiv.style.color = '#16a34a';
                messageDiv.style.display = 'block';
                
                // Visual feedback on button
                btn.textContent = 'Applied!';
                btn.style.background = '#16a34a';
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = '';
                }, 2000);
            } else {
                 if (!code) {
                     messageDiv.textContent = 'Promo code removed.';
                     messageDiv.style.color = '#16a34a';
                 } else {
                     messageDiv.textContent = 'Invalid promo code used.';
                     messageDiv.style.color = '#ef4444';
                 }
                 messageDiv.style.display = 'block';
            }
            updateSummary(data.summary);
        } else {
             messageDiv.textContent = 'Error applying code.';
             messageDiv.style.color = '#ef4444';
             messageDiv.style.display = 'block';
        }
    })
    .catch(err => {
        console.error('Error applying promo:', err);
        btn.textContent = originalText;
        btn.disabled = false;
        messageDiv.textContent = 'System error. Try again.';
        messageDiv.style.color = '#ef4444';
        messageDiv.style.display = 'block';
    });
}
