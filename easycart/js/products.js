document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const navSearchForm = document.querySelector('.nav-search');
    const grid = document.getElementById('productGrid');
    
    if (!filterForm || !grid) return;

    const checkboxes = filterForm.querySelectorAll('input[type="checkbox"]');
    const hiddenSearchInput = filterForm.querySelector('input[name="search"]');
    const navSearchInput = navSearchForm ? navSearchForm.querySelector('input[name="search"]') : null;

    // Handle AJAX filtering
    function triggerUpdate() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        
        // Remove empty search to keep URL clean
        if (params.get('search') === '') {
            params.delete('search');
        }

        // Update Browser URL (History API)
        const newUrl = window.location.pathname + '?' + params.toString();
        window.history.pushState({}, '', newUrl);
        
        // Add AJAX flag
        params.append('ajax', '1');
        
        // Add a loading state
        grid.style.opacity = '0.5';
        
        fetch('products.php?' + params.toString())
            .then(response => response.text())
            .then(html => {
                grid.innerHTML = html;
                grid.style.opacity = '1';
                
                const count = grid.querySelectorAll('.product-card').length;
                const productCountElem = document.getElementById('productCount');
                const headerCountElem = document.getElementById('headerCount');
                if (productCountElem) productCountElem.textContent = count;
                if (headerCountElem) headerCountElem.textContent = `(${count})`;
            })
            .catch(error => {
                console.error('Error:', error);
                grid.style.opacity = '1';
            });
    }

    // Checkbox changes
    checkboxes.forEach(cb => {
        cb.addEventListener('change', triggerUpdate);
    });

    // Real-time Search Sync (Header -> Sidebar)
    if (navSearchInput && hiddenSearchInput) {
        navSearchInput.addEventListener('input', function() {
            hiddenSearchInput.value = this.value;
            // We don't triggerUpdate here to avoid too many requests while typing
            // unless the user stops typing for a bit (debounce would be better)
        });
    }

    // Header Search Submit
    if (navSearchForm) {
        navSearchForm.addEventListener('submit', function(e) {
            if (window.location.pathname.includes('products.php')) {
                e.preventDefault();
                hiddenSearchInput.value = navSearchInput.value;
                triggerUpdate();
            }
        });
    }
});

// --- Quick Add To Cart Logic ---
function updateQuickQty(productId, change, isHome = false) {
    const card = document.querySelector(`.product-card[data-id="${productId}"]`);
    if (!card) return;

    const container = card.querySelector('.quick-add-container');
    const display = container.querySelector('.qty-display');
    let currentQty = display ? parseInt(display.textContent) : 0;
    let newQty = currentQty + change;
    
    if (newQty < 0) newQty = 0;

    // Use correct path based on page
    const ajaxPath = isHome ? 'php/cart.php' : 'cart.php';

    // Optimistic UI Update
    if (newQty > 0) {
        container.innerHTML = `
            <div class="qty-selector">
                <button class="btn-qty btn-minus" onclick="updateQuickQty(${productId}, -1, ${isHome})">-</button>
                <span class="qty-display">${newQty}</span>
                <button class="btn-qty btn-plus" onclick="updateQuickQty(${productId}, 1, ${isHome})">+</button>
            </div>
        `;
    } else {
        container.innerHTML = `
            <button class="btn btn-quick-add" onclick="updateQuickQty(${productId}, 1, ${isHome})">
                <i class="fas fa-plus"></i> Add to Cart
            </button>
        `;
    }

    // Server Sync
    const formData = new FormData();
    formData.append('action', 'update_qty');
    formData.append('product_id', productId);
    formData.append('change', change);

    fetch(ajaxPath, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert('Failed to update cart. Please try again.');
            location.reload(); 
        }
    })
    .catch(err => {
        console.error('Error:', err);
        location.reload();
    });
}




