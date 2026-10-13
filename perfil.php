<?php

session_start();

// Comprobar que el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {

    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
</head>

<body>

<h1>Mi perfil</h1>

<p>
    Has iniciado sesión correctamente.
</p>

<p>
    Correo:
    <?= htmlspecialchars(
        $_SESSION['email'],
        ENT_QUOTES,
        'UTF-8'
    ) ?>
</p>

<br>

<a href="logout.php">
    Cerrar sesión
</a>

</body>
</html>
