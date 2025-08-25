// resources/js/cart.js
let cartSidebarOpen = false;

function toggleCartSidebar() {
    cartSidebarOpen = !cartSidebarOpen;
    if (window.Livewire) {
        try {
            window.Livewire.dispatch('toggleCart');
        } catch (e) {
            window.Livewire.emit('toggleCart');
        }
    }
}

function updateCartCount() {
    fetch('/api/cart-count')
        .then(response => response.json())
        .then(data => {
            const cartCountElement = document.getElementById('cartCount');
            if (cartCountElement) {
                if (data.count > 0) {
                    cartCountElement.textContent = data.count;
                    cartCountElement.style.display = 'flex';
                } else {
                    cartCountElement.style.display = 'none';
                }
            }
        })
        .catch(error => console.log('Cart count error:', error));
}

function addToCartSession(productId, name, price, image = '') {
    fetch('/api/add-to-cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            name: name,
            price: price,
            image: image
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount();
            showSuccessMessage(name + ' added to cart!');
            if (window.Livewire) {
                try {
                    window.Livewire.dispatch('cartUpdated');
                } catch (e) {
                    window.Livewire.emit('cartUpdated');
                }
            }
        }
    })
    .catch(error => {
        console.log('Add to cart error:', error);
        showSuccessMessage('Error adding item to cart');
    });
}

function showSuccessMessage(message) {
    const messageDiv = document.createElement('div');
    messageDiv.innerHTML = message;
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        z-index: 1000;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        font-weight: 600;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        if (document.body.contains(messageDiv)) {
            messageDiv.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => {
                if (document.body.contains(messageDiv)) {
                    document.body.removeChild(messageDiv);
                }
            }, 300);
        }
    }, 3000);
}

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Make functions globally available
window.toggleCartSidebar = toggleCartSidebar;
window.addToCartSession = addToCartSession;
window.updateCartCount = updateCartCount;

// Initialize on page load
document.addEventListener('DOMContentLoaded', updateCartCount);

// Listen for Livewire events
if (window.Livewire) {
    window.addEventListener('cartUpdated', updateCartCount);
}