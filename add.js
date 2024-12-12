 // Función para agregar un producto al carrito
 function addToCart(name, price) {
    const pizza = {
        name: name,
        price: price,
        quantity: 1
    };

    if (!sessionStorage.getItem('cart')) {
        sessionStorage.setItem('cart', JSON.stringify([]));
    }

    let cart = JSON.parse(sessionStorage.getItem('cart'));
    let existingPizzaIndex = cart.findIndex(item => item.name === name);

    if (existingPizzaIndex >= 0) {
        cart[existingPizzaIndex].quantity += 1;
    } else {
        cart.push(pizza);
    }

    sessionStorage.setItem('cart', JSON.stringify(cart));
    updateCartView();
}

// Función para actualizar la vista del carrito desplegable
function updateCartView() {
    let cart = JSON.parse(sessionStorage.getItem('cart'));

    let cartCount = document.getElementById('cart-count');
    cartCount.textContent = cart.length; // Muestra la cantidad de productos en el carrito

    let cartItems = document.getElementById('cart-items');
    cartItems.innerHTML = ''; // Limpiar la lista de productos

    let totalPrice = 0;

    cart.forEach(item => {
        let li = document.createElement('li');
        li.innerHTML = `${item.name} x ${item.quantity} - $${(item.price * item.quantity).toFixed(2)}`;
        cartItems.appendChild(li);
        totalPrice += item.price * item.quantity;
    });

    let totalPriceElement = document.getElementById('cart-total-price');
    totalPriceElement.textContent = totalPrice.toFixed(2); // Mostramos el total
}