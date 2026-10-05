# ------------------------------------------------------------------
# CONEXIÓN A BASE DE DATOS CON PDO
# ------------------------------------------------------------------

# ❌ MAL: credenciales hardcodeadas en el código.
# ✅ DEBERÍA SER: leerlas de variables de entorno o de un archivo .env
#    fuera del repositorio, por ejemplo:
#    $host = getenv('DB_HOST');
#    $db   = getenv('DB_NAME');
#    $user = getenv('DB_USER');
#    $pass = getenv('DB_PASS');
$host = 'localhost';   # Host donde está el servidor MySQL/MariaDB.
$db   = 'usuarios';    # Nombre de la base de datos a la que conectar.
$user = 'webuser';     # Usuario de MySQL con permisos mínimos sobre esa BD.
$pass = 'UnaClaveBD-MuySegura-123!';  # ❌ Contraseña en texto plano dentro del código.
                                      # ✅ DEBERÍA SER: variable de entorno, nunca en el repo.

# Bloque try: intenta ejecutar la conexión y capturar errores si falla.
try {

    # Creamos la instancia PDO. Guarda la conexión en $pdo para reutilizarla.
    $pdo = new PDO(
        # DSN: indica driver (mysql), host, nombre de BD y charset.
        # utf8mb4 es el correcto (soporta emojis y Unicode completo).
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,   # Usuario de la BD.
        $pass,   # Contraseña de la BD.
        [        # Array de opciones de configuración de la conexión.

            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            # desactiva la emulación de prepared statements.
            # Las consultas preparadas se ejecutan de verdad en MySQL,
            # lo que mejora la seguridad frente a inyección SQL.
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

# Captura cualquier PDOException lanzada durante la conexión.
# Ejemplos: credenciales malas, BD inexistente, servidor caído.
} catch (PDOException $e) {

    # no mostrar detalles reales del error al usuario.
    # Guardamos el mensaje real en el log del servidor para diagnóstico.
    # error_log() por defecto escribe en el log del sistema PHP.
    # configurar un log propio de la aplicación y no depender
    # del log global del servidor.
    error_log($e->getMessage());

    # mensaje genérico al usuario. No filtra info sensible
    # (usuario, host, nombre de BD, versión, etc.).
    die("Error interno del servidor.");
}