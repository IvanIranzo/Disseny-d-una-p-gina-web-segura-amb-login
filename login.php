<?php

session_start();
# Inicia o reanuda la sesión. Debe ir antes de cualquier salida.

require_once 'config.php';
# Carga la configuración y la conexión $pdo. Evita duplicados.
# ⚠️ Si no existe config.php, error fatal.

$error = '';
# Inicializa la variable para evitar "undefined variable" en el HTML.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
# Solo procesa si el formulario se envió por POST.

    $email = trim($_POST['email'] ?? '');
    # Limpia espacios y evita "undefined index".

    $password = $_POST['password'] ?? '';
    # Valor por defecto vacío si no se envía.

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    # Valida el formato del email.

        $error = "Correo o contraseña incorrectos.";
        # Mensaje genérico: no revela si el email existe.

    } else {

        // Consulta preparada
        $sql = "
            SELECT id, email, password
            FROM usuarios
            WHERE email = :email
            LIMIT 1
        ";
        # Prepared statement: evita inyección SQL.

        $stmt = $pdo->prepare($sql);
        # Prepara la consulta.

        $stmt->execute([
            ':email' => $email
        ]);
        # Ejecuta con el parámetro vinculado de forma segura.

        $usuario = $stmt->fetch();
        # Obtiene una fila (o false si no hay resultados).

        if (
            $usuario &&
            password_verify($password, $usuario['password'])
        ) {
        # Comprueba usuario Y contraseña con hash. Correcto.

            // Evita session fixation
            session_regenerate_id(true);
            # Regenera el ID de sesión tras login. Excelente práctica.

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['email'] = $usuario['email'];
            # Guarda datos mínimos en sesión.

            header('Location: perfil.php');
            exit;
            # Redirige y detiene la ejecución.
        }

        // No indicamos si el email existe o no.
        $error = "Correo o contraseña incorrectos.";
        # Mismo mensaje para email inexistente o contraseña errónea.
    }
}

?>

<!DOCTYPE html>
<html lang="es">
# Idioma correcto.

<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
</head>

<body>

<h1>Iniciar sesión</h1>

<?php if ($error): ?>

    <p>
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </p>
    # Escapado correcto al mostrar el error.

<?php endif; ?>

<form method="POST" action="login.php">

    <label for="email">
        Correo:
    </label>

    <br>

    <input
        type="email"
        id="email"
        name="email"
        maxlength="255"
        required
    >
    # ✅ Correcto.
    # ⚠️ MEJOR: añadir autocomplete="email" y conservar el valor con value.

    <br><br>

    <label for="password">
        Contraseña:
    </label>

    <br>

    <input
        type="password"
        id="password"
        name="password"
        required
    >
    # ✅ Correcto.
    # ⚠️ MEJOR: añadir autocomplete="current-password".

    <br><br>

    <button type="submit">
        Iniciar sesión
    </button>
</form>

<br>

<a href="registro.php">
   # Crear una cuenta
</a>

</body>
</html>