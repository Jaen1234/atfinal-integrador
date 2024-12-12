<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Iniciar Sesion / Registrarse></title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/navegacion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Iconos de FontAwesome para los íconos de la barra derecha -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
</head>
<body class="loginbody">

    <!-- Barra de navegación -->
    <header class="navbar-top">
        <nav class="navbar-container">
            <!-- Logo a la izquierda -->
            <div class="logo">
                <a href="index.php" class="logo" onclick="reloadPage();">
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
            session_start();
            if (!isset($_SESSION['user'])) {
                echo '<li><a href="login.php"><i class="fas fa-user"></i>Hola, <br>Iniciar sesión</a></br></li>';
            } else {
                // Si está logueado, muestra su nombre
                echo '<li>Hola, ' . $_SESSION['user'] . '</li>';
                echo '<li><a href="logout.php">Cerrar sesión</a></li>';
            }
            ?>
            <li><a href="#"><i class="fas fa-shopping-cart"></i> Carrito</a></li>
            </ul>
            
        </nav>
        </header>
        <div class="form-container">
        <div class="form-block login-block">
            <!-- Formulario de Iniciar Sesión -->
            <div class="form-content login-form">
                <h3>Iniciar sesión</h3>
                <form method="POST" action="login_register.php">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required>
                    
                    <label for="contraseña">Contraseña</label>
                    <input type="password" id="password" name="contraseña" required>
                    
                    <button type="submit" name="login">Iniciar sesión</button>
                </form>
            </div>
        </div>
    
        <div class="form-block register-block">
            <!-- Bloque para Crear Cuenta -->
            <div class="form-content register-form">
                <h3>Crear cuenta</h3>
                <p>Si no tienes una cuenta, puedes crear una <br>fácilmente.</br></p>
                <button class="create-account-btn" onclick="window.location.href='registro.php';">Crear cuenta</button>
            </div>
        </div>
    </div>
        
        </body>

    

</html>