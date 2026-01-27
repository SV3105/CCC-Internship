function toggleWishlist(productId, btn, isHome = false) {
    const icon = btn.querySelector('i');
    const textSpan = btn.querySelector('.wishlist-text');
    const isRemoveBtn = btn.classList.contains('btn-wishlist-remove');
    const isAdding = !isRemoveBtn && !icon.classList.contains('fas'); 
    
    if (!isRemoveBtn) {
        if (isAdding) {
            icon.classList.remove('far');
            icon.classList.add('fas', 'active-wishlist');
            if (textSpan) textSpan.textContent = 'In Wishlist';
        } else {
            icon.classList.remove('fas', 'active-wishlist');
            icon.classList.add('far');
            if (textSpan) textSpan.textContent = 'Add to Wishlist';
        }
    } else {
        // We are on the wishlist page and the X button was clicked
        const card = btn.closest('.product-card');
        if (card) {
            card.style.opacity = '0.5';
            card.style.transform = 'scale(0.9)';
            setTimeout(() => card.remove(), 300);
        }
    }

    const ajaxPath = isHome ? 'php/wishlist.php' : 'wishlist.php';

    const formData = new FormData();
    formData.append('action', (isAdding || !isRemoveBtn && icon.classList.contains('fas')) ? 'add' : 'remove');
    if (isRemoveBtn) formData.set('action', 'remove');
    formData.append('product_id', productId);

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
            alert('Failed to update wishlist. Please try again.');
            location.reload(); 
        }
    })
    .catch(err => {
        console.error('Error:', err);
        location.reload();
    });
}
