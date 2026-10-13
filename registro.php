<?php

session_start();

require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validar correo
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Datos incorrectos.";

    // Validar contraseña
    } elseif (strlen($password) < 8) {

        $error = "La contraseña debe tener al menos 8 caracteres.";

    } else {

        // Crear hash seguro de la contraseña
        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        try {

            // Consulta preparada
            $sql = "
                INSERT INTO usuarios (email, password)
                VALUES (:email, :password)
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':email' => $email,
                ':password' => $passwordHash
            ]);

            header('Location: login.php');
            exit;

        } catch (PDOException $e) {

            // 23000 = violación de una restricción,
            // en este caso el email UNIQUE.
            if ($e->getCode() == 23000) {

                $error = "No se ha podido crear la cuenta.";

            } else {

                error_log($e->getMessage());

                $error = "Error interno del servidor.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro</title>
</head>

<body>

<h1>Crear cuenta</h1>

<?php if ($error): ?>

    <p>
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </p>

<?php endif; ?>

<form method="POST" action="registro.php">

    <label for="email">Correo:</label>

    <br>

    <input
        type="email"
        id="email"
        name="email"
        maxlength="255"
        required
    >

    <br><br>

    <label for="password">Contraseña:</label>

    <br>

    <input
        type="password"
        id="password"
        name="password"
        minlength="8"
        required
    >

    <br><br>

    <button type="submit">
        Registrarse
    </button>

</form>

<br>

<a href="login.php">
    Ya tengo una cuenta
</a>

</body>
</html>
