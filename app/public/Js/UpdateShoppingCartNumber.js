    // Initialize the shopping cart
    const shoppingCart = document.getElementById('items-in-shoppingCart');

    const updateCartCount = async () => {
        try {
            const response = await fetch('/getNumberOfCartItems');
            const data = await response.json();
            if (data.success) {
                const itemCount = data.numberOfItems;
                if (itemCount > 0) {
                    shoppingCart.textContent = itemCount;
                    shoppingCart.classList.remove('hidden');
                } else {
                    shoppingCart.classList.add('hidden');
                }
            } else {
                console.error('Failed to fetch cart count:', data.message);
            }
        } catch (error) {
            console.error('Error fetching cart count:', error);
        }
    };

    if (shoppingCart) {
        updateCartCount();
    }