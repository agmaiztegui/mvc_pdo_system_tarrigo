<?php
class Model {
    private $pdo;

    public function __construct() {
        $host = '127.0.0.1';
        $db = 'login_system2';
        $user = 'login_escritor';
        $pass = 'writeAccess';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    public function isEmailOrDniTaken($dni, $email) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE dni = ? OR email = ?");
        $stmt->execute([$dni, $email]);
        return $stmt->fetch() !== false;
    }
    
    public function checkEmailExists($email) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function createUser($dni, $nombre, $apellido, $fecha_nacimiento, $email, $password) {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (dni, nombre, apellido, fecha_nacimiento, email, password, role)
            VALUES (?, ?, ?, ?, ?, ?, 'user')
        ");
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        return $stmt->execute([$dni, $nombre, $apellido, $fecha_nacimiento, $email, $hashedPassword]);
    }

    public function login($dniOrEmail, $password) {
        $dniOrEmail = rtrim($dniOrEmail);

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE dni = ? OR email = ?");
        $stmt->execute([$dniOrEmail, $dniOrEmail]);
        $user = $stmt->fetch();
    
        if ($user && password_verify($password, $user['password']) && $user['locked'] == 0) {
            // Inicio de sesión exitoso
            $_SESSION['user'] = $user;
            $this->logAccess($user['id']);
            if ($user['role'] === 'admin') {
                header('Location: view/admin_dashboard.php');
            } else {
                header('Location: view/user_dashboard.php');
            }
            exit();
        } else {
            // Inicio de sesión fallido
            if ($user && $user['locked'] == 1) {
                return 'Tu usuario se bloqueó, contacta al administrador';
            } else {
                $this->incrementFailedLogins($dniOrEmail);
                $failedLogins = $this->getFailedLogins($dniOrEmail);
                if ($failedLogins >= 3) {
                    $this->blockUser($dniOrEmail);
                    return 'Tu usuario se bloqueó, contacta al administrador';
                } else {
                    return 'Credenciales incorrectas';
                }
            }
        }
    }
    
    public function logAccess($userId) {
        $stmt = $this->pdo->prepare("INSERT INTO access_logs (user_id, access_time) VALUES (?, NOW())");
        $stmt->execute([$userId]);

        $errores = $stmt->errorInfo();
        if( !empty($errores) ){
            echo '</br>Codigo de error PDO:'.implode(":",$this->pdo->errorInfo());
        }

    }
    
    public function incrementFailedLogins($dniOrEmail) {
        $stmt = $this->pdo->prepare("UPDATE users SET intentosfallidos = intentosfallidos + 1 WHERE (dni = ? OR email = ?)");
        $stmt->execute([$dniOrEmail, $dniOrEmail]);

        $errores = $stmt->errorInfo();
        if( !empty($errores) ){
            // echo '</br>Codigo de error PDO:'.implode(":",$this->pdo->errorInfo());
        }
    }
    
    public function getFailedLogins($dniOrEmail) {
        $stmt = $this->pdo->prepare("SELECT intentosfallidos FROM users WHERE dni = ? OR email = ?");
        $stmt->execute([$dniOrEmail, $dniOrEmail]);
        $user = $stmt->fetch();

        if($user === FALSE){
            // No encontré nada.
        } else {
            return $user['intentosfallidos'];
        }
        return $user['intentosfallidos'];
    }
    
    public function blockUser($dniOrEmail) {
        $stmt = $this->pdo->prepare("UPDATE users SET locked = 1 WHERE dni = ? OR email = ?");
        $stmt->execute([$dniOrEmail, $dniOrEmail]);
    }

    public function buscar_logs($email) {
      $stmt = $this->pdo->prepare("select access_logs.*, users.email as email from access_logs join users on (users.id = access_logs.user_id) where users.email = ?");
      $stmt->execute([$email]);
      $logs = $stmt->fetchAll();
      // var_dump($logs);
      return $logs;
    }

    public function getUsers(){
        $stmt = $this->pdo->prepare("SELECT id, dni, nombre, apellido, fecha_nacimiento, email, role, locked, intentosfallidos FROM users");
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function deleteUser($userID_a_eliminar){
        $stmt = $this->pdo->prepare("DELETE users WHERE id = ?");
        try{
            $stmt->execute([$userID_a_eliminar]);
            return 0;
        } catch (Exception $e) {
            echo $e;
            return -1;
        }
    }

    public function unlockUser($userID_a_desbloquear){
        $stmt = $this->pdo->prepare("UPDATE users SET locked = 0 WHERE id = ?");
        try {
            $stmt->execute([$userID_a_desbloquear]);
            $this->restartFailedLogins($userID_a_desbloquear);
            return 0;
        } catch (Exception $e) {
            echo $e;
            return -1;
        }
    }


    public function restartFailedLogins($idDelUsuario) {
        $stmt = $this->pdo->prepare("UPDATE users SET intentosfallidos = 0 WHERE id = ?");
        $stmt->execute([$idDelUsuario]);

        $errores = $stmt->errorInfo();
        if( !empty($errores) ){
            // echo '</br>Codigo de error PDO:'.implode(":",$this->pdo->errorInfo());
        }
    }
}
?>
