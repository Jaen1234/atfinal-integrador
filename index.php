<?php
session_start();
include('conexion.php'); // Conectar con la base de datos


// Verificar si el carrito existe en la sesión
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // Inicializar el carrito como un array vacío
}


// Obtener las pizzas desde la base de datos
$query = "SELECT * FROM pizzas";
$stmt = $pdo->query($query);
$pizzas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Menu principal</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/conten-carrito.css">
    <link rel="stylesheet" href="css/navegacion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Iconos de FontAwesome para los íconos de la barra derecha -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
</head>
<body>

    <!-- Barra de navegación -->
    <header class="navbar-top">
        <nav class="navbar-container">
            <!-- Logo a la izquierda -->
            <div class="logo">
                <a href="#" class="logo" onclick="reloadPage();">
                    <img src="img/margoniwhite.png" alt="Logo" width="150">
                </a>
               
            </div>

            <!-- Enlaces de navegación centrados -->
            <ul class="nav-links">
                <li><a href="index.php">Menú</a></li>
                <li><a href="mispedidos.html">Pedidos</a></li>
                <li><a href="#">Locales</a></li>
            </ul>

            <!-- Barra de navegación derecha con íconos -->
            <ul class="nav-links-right">
                <li><a href="tel:+123456789"><i class="fas fa-phone-alt"></i> Llámanos<br>902442742</br></a></li>
                <li><a href="https://wa.me/123456789"><i class="fab fa-whatsapp"></i> Pide por <br>WhatsApp</br></a></li>
                <?php
             
            
            if (!isset($_SESSION['user'])) {
                echo '<li><a href="login.php"><i class="fas fa-user"></i>Hola, <br>Iniciar sesión</br></a></li>';
            } else {
                // Si está logueado, muestra su nombre
                echo '<li>Hola, ' . $_SESSION['user'] . '</li>';
                echo '<li><a href="logout.php">Cerrar sesión</a></li>';
            }
            ?>
             <!-- Icono de carrito -->
             <li>
                    <a href="#" id="cart-icon" class="cart-icon" onclick="toggleCartDropdown();">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cart-count"><?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span> <!-- Muestra la cantidad de productos -->
                    </a>

                    <!-- Contenedor del carrito desplegable -->
                    <div id="cart-dropdown" class="cart-dropdown" style="display: none;">
                        <ul id="cart-items">
                            <!-- Aquí se agregarán dinámicamente los productos del carrito -->
                        </ul>
                        
                        <div id="cart-total">
                            <strong>Total: $<span id="cart-total-price">0.00</span></strong>
                        </div>
                        <a href="cart.php" class="view-cart-btn">Ver carrito completo</a>
                    </div>
                </li>

            </ul>
            
        </nav>
    

    </header>

     <!-- Contenedor para las imágenes desplazables -->
     <section class="image-slider">
        <div class="slider-container">
            <!-- Imagenes dentro del slider -->
            <img src="img/banner1.jpg" alt="Imagen 1" class="slider-image">
            <img src="img/banner2.jpg" alt="Imagen 2" class="slider-image">
            
        </div>

        <!-- Botones de desplazamiento (flechas) -->
        <button class="slider-btn left-btn" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
        <button class="slider-btn right-btn" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
    </section>
    <!-- Título y texto debajo del carrusel -->
    <section class="explore-menu">
        <h2>EXPLORA NUESTRO MENÚ</h2>
        <p>Disfruta de los sabores inigualables de nuestras pizzas
           hechas con los mejores ingredientes
        </p>
    </section>
    <!-- Bloques de pizzas -->
<section class="pizza-blocks">
    <div class="pizza-block">
        <img src="img/combo1.jpg" alt="Pizza 1" class="pizza-image">
        <p class="pizza-name">Pizzas</p>
    </div>
    <div class="pizza-block">
        <img src="img/combo2.jpg" alt="Pizza 2" class="pizza-image">
        <p class="pizza-name">Pizza Peperoni</p>
    </div>
    <div class="pizza-block">
        <img src="img/combo3.jpg" alt="Pizza 3" class="pizza-image">
        <p class="pizza-name">Promociones</p>
    </div>
    <div class="pizza-block">
        <img src="img/combo4.jpg" alt="Pizza 4" class="pizza-image">
        <p class="pizza-name">Combos</p>
    </div>
</section>
<!-- Título y texto debajo de los bloques de pizza -->
<section class="promotions-section">
    <h3>PROMOCIONES</h3>
    <p>Sabores para todos los gustos</p>
</section>
<section class="pizza-options">
<?php foreach ($pizzas as $pizza): ?>
    <div class="pizza-option">
        <img src="<?= $pizza['image']; ?>" alt="<?= $pizza['name']; ?>">
        <div class="pizza-info">
            <h4><?= $pizza['name']; ?></h4>
            <p><?= $pizza['description']; ?></p>
            <p class="pizza-price">$<?= number_format($pizza['price'], 2); ?></p>
            <button class="add-to-order" 
            onclick="addToCart('<?= $pizza['name']; ?>', <?= $pizza['price']; ?>, '<?= $pizza['image']; ?>')">Agregar al pedido</button>
        </div>
    </div>
<?php endforeach; ?>
    
    
    <div class="pizza-option">
        <img src="img/combo2.jpg" alt="Pizza 2">
        <div class="pizza-info">
            <h4>Pizza Pepperoni</h4>
            <p>Pizza con salsa de tomate, queso mozzarella y abundante pepperoni.</p>
            <p class="pizza-price">$10.99</p>
            <a href="#" class="add-to-order">Agregar al pedido</a>
        </div>
    </div>
</section>
<!-- Subtítulo y párrafo debajo de los bloques -->
<section class="pizzas-text">
    <h2>Pizzas</h2>
    <p>Sabores únicos que no te puedes perder</p>
</section>
<!-- Sección de los dos bloques adicionales -->
<section class="pizza-options">
    <div class="pizza-option">
        <img src="img/combo2.jpg" alt="Pizza 3">
        <div class="pizza-info">
            <h4>Pizza Hawaiana</h4>
            <p>Una combinación perfecta de jamón, piña y queso.</p>
            <p class="pizza-price">$10.99</p>
            <a href="#" class="add-to-order">Agregar al pedido</a>
        </div>
    </div>

    <div class="pizza-option">
        <img src="img/combo2.jpg" alt="Pizza 4">
        <div class="pizza-info">
            <h4>Pizza Cuatro Quesos</h4>
            <p>Una deliciosa mezcla de quesos para los amantes del sabor intenso.</p>
            <p class="pizza-price">$10.99</p>
            <a href="#" class="add-to-order">Agregar al pedido</a>
        </div>
    </div>
</section>
<!-- Nueva sección: Combos -->
<section class="combos-text">
    <h2>Combos</h2>
    <p>Disfruta de nuestros combos especiales para disfrutar en familia o con amigos.</p>
</section>
<!-- Sección de los dos bloques de combos -->
<section class="pizza-options">
    <div class="pizza-option">
        <img src="img/combo2.jpg" alt="Combo 1">
        <div class="pizza-info">
            <h4>Combo Familiar</h4>
            <p>Incluye 2 pizzas grandes, papas fritas y bebidas para 4 personas.</p>
            <p class="pizza-price">$10.99</p>
            <a href="#" class="add-to-order">Agregar al pedido</a>
        </div>
    </div>

    <div class="pizza-option">
        <img src="img/combo3.jpg" alt="Combo 2">
        <div class="pizza-info">
            <h4>Combo Pareja</h4>
            <p>Disfruta de 1 pizza mediana y 2 bebidas.</p>
            <p class="pizza-price">$10.99</p>
            <a href="#" class="add-to-order">Agregar al pedido</a>
        </div>
    </div>
</section>
<!-- Nueva sección: BIG COMBOS -->
<section class="big-combos-text">
    <h2>BIG COMBOS</h2>
    <p>Disfruta de nuestros combos exclusivos y grandes, perfectos para compartir en grupo.</p>
</section>
<!-- Sección de los dos bloques de BIG COMBOS -->
<section class="pizza-options">
    <div class="pizza-option">
        <img src="img/combo4.jpg" alt="Big Combo 1">
        <div class="pizza-info">
            <h4>Big Combo Familiar</h4>
            <p>Incluye 3 pizzas grandes, 2 porciones de papas y 4 bebidas para un gran grupo.</p>
            <p class="pizza-price">$10.99</p>
            <a href="#" class="add-to-order">Agregar al pedido</a>
        </div>
    </div>

    <div class="pizza-option">
        <img src="img/combo4.jpg" alt="Big Combo 2">
        <div class="pizza-info">
            <h4>Big Combo para Amigos</h4>
            <p>Un combo especial con 4 pizzas medianas, ensalada y bebidas.</p>
            <p class="pizza-price">$10.99</p>
            <a href="#" class="add-to-order">Agregar al pedido</a>
        </div>
    </div>
</section>

 

    <!-- Script para controlar el slider (si deseas que sea automático) -->
    <script>
        let sliderContainer = document.querySelector('.slider-container');
        let images = document.querySelectorAll('.slider-image');
        let index = 0;

        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        // Función para actualizar el desplazamiento
        function updateSlider() {
            sliderContainer.style.transform = `translateX(-${index * 100}%)`;
        }

        // Controlar el clic en el botón izquierdo
        prevBtn.addEventListener('click', () => {
            if (index > 0) {
                index--;
            } else {
                index = images.length - 1; // Si estamos en la primera imagen, volvemos a la última
            }
            updateSlider();
        });

        // Controlar el clic en el botón derecho
        nextBtn.addEventListener('click', () => {
            if (index < images.length - 1) {
                index++;
            } else {
                index = 0; // Si estamos en la última imagen, volvemos a la primera
            }
            updateSlider();
        });

        // Desplazamiento automático cada 3 segundos (opcional)
        setInterval(() => {
            if (index >= images.length - 1) {
                index = 0;
            } else {
                index++;
            }
            updateSlider();
        }, 5000);
    </script>
    
    <script>
     // Función para recargar la página actual
    function reloadPage() {
        location.reload(); // Recarga la página actual
    }
     
    </script>
    <script>
// Función para mostrar/ocultar el carrito
function toggleCartDropdown() {
    const cartDropdown = document.getElementById('cart-dropdown');
    
    // Cambiar el estado de visibilidad (si está visible, lo ocultamos, y si está oculto, lo mostramos)
    if (cartDropdown.style.display === 'block') {
        cartDropdown.style.display = 'none'; // Ocultar el carrito
    } else {
        cartDropdown.style.display = 'block'; // Mostrar el carrito
    }
}




</script>

    

  <script>
    // Función para agregar un producto al carrito
    function addToCart(name, price,image) {
    // Verificar si el usuario está logueado
    <?php if (!isset($_SESSION['user'])): ?>
        // Si no está logueado, mostrar un mensaje de alerta y redirigir al login
        Swal.fire({
            title: '¡Debes iniciar sesión!',
            text: 'Para agregar productos al carrito, por favor inicia sesión.',
            icon: 'warning',
            confirmButtonText: 'Iniciar sesión',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'login.php'; // Redirige al login
            }
        });
    <?php else: ?>
        // Si está logueado, proceder con la lógica para agregar al carrito
        const pizza = {
            name: name,
            price: price,
            quantity: 1
            
            
        };

        // Obtener carrito desde sessionStorage
        let cart = JSON.parse(sessionStorage.getItem('cart')) || [];

        // Verificar si la pizza ya está en el carrito
        let existingPizzaIndex = cart.findIndex(item => item.name === name);

        if (existingPizzaIndex >= 0) {
            // Si la pizza ya existe, incrementamos la cantidad
            cart[existingPizzaIndex].quantity += 1;
        } else {
            // Si la pizza no existe, la agregamos al carrito
            cart.push(pizza);
        }

        // Guardamos el carrito actualizado en sessionStorage
        sessionStorage.setItem('cart', JSON.stringify(cart));

        // Actualizar la vista del carrito
        updateCartView();

        // Alerta de producto agregado
        Swal.fire({
            title: '¡Producto agregado!',
            text: 'Tu producto ha sido agregado al carrito.',
            icon: 'success',
            confirmButtonText: 'Cerrar',
            timer: 3000 // Desaparece después de 3 segundos
        });
    <?php endif; ?>
}



// Función para aumentar la cantidad de una pizza en el carrito
function increaseQuantity(index) {
    let cart = JSON.parse(sessionStorage.getItem('cart'));
    cart[index].quantity += 1; // Incrementar cantidad
    sessionStorage.setItem('cart', JSON.stringify(cart)); // Guardar cambios
    updateCartView(); // Actualizar vista
}

// Función para disminuir la cantidad de una pizza en el carrito
function decreaseQuantity(index) {
    let cart = JSON.parse(sessionStorage.getItem('cart'));
    if (cart[index].quantity > 1) {
        cart[index].quantity -= 1; // Decrementar cantidad
    }
    sessionStorage.setItem('cart', JSON.stringify(cart)); // Guardar cambios
    updateCartView(); // Actualizar vista
}


  // Función para actualizar la vista del carrito desplegable
function updateCartView() {
    let cart = JSON.parse(sessionStorage.getItem('cart'));
    let cartCount = document.getElementById('cart-count');
    cartCount.textContent = cart.length; // Muestra la cantidad de productos en el carrito

    let cartItems = document.getElementById('cart-items');
    cartItems.innerHTML = ''; // Limpiar la lista de productos

    let totalPrice = 0;

    cart.forEach((item, index) => {
        let li = document.createElement('li');
        li.innerHTML = `${item.name} - $${item.price.toFixed(2)} x ${item.quantity}`;

        
        // Crear contenedor de cantidad
        let quantityContainer = document.createElement('div');
        quantityContainer.classList.add('quantity-container'); // Añadimos la clase para estilizar

        let decreaseBtn = document.createElement('button');
        decreaseBtn.textContent = '-';
        decreaseBtn.classList.add('quantity-btn');
        decreaseBtn.onclick = function() {
            decreaseQuantity(index);
        };

        let quantityText = document.createElement('span');
        quantityText.textContent = item.quantity;

        let increaseBtn = document.createElement('button');
        increaseBtn.textContent = '+';
        increaseBtn.classList.add('quantity-btn');
        increaseBtn.onclick = function() {
            increaseQuantity(index);
        };

        // Añadir los botones de cantidad al contenedor
        quantityContainer.appendChild(decreaseBtn);
        quantityContainer.appendChild(quantityText);
        quantityContainer.appendChild(increaseBtn);

        // Añadir el contenedor al li
        li.appendChild(quantityContainer);

        // Crear un botón para eliminar el producto del carrito
        let deleteBtn = document.createElement('button');
        deleteBtn.textContent = '🗑️'; // Icono de basura
        deleteBtn.classList.add('delete-item'); // Añadir clase para estilo
        deleteBtn.onclick = function() {
            removeFromCart(index); // Eliminar el producto del carrito
        };

        // Añadir el botón de eliminar al elemento de la lista
        li.appendChild(deleteBtn);
        cartItems.appendChild(li);

        totalPrice += item.price * item.quantity;
    });

    let totalPriceElement = document.getElementById('cart-total-price');
    totalPriceElement.textContent = totalPrice.toFixed(2); // Mostramos el total
}



    // Función para eliminar un producto del carrito
    function removeFromCart(index) {
        let cart = JSON.parse(sessionStorage.getItem('cart'));
        
        // Eliminar el producto del carrito usando su índice
        cart.splice(index, 1);
        
        // Guardar el carrito actualizado en sessionStorage
        sessionStorage.setItem('cart', JSON.stringify(cart));
        
        // Actualizar la vista del carrito
        updateCartView();
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    updateCartView(); // Actualiza el carrito al cargar la página
});
</script>
    
</body>
</html>
