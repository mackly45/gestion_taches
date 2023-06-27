<?php
// Classe de connexion à la base de données
class Database {
    private $host = "localhost";
    private $dbname = "taches_projet";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            echo "Erreur de connexion à la base de données : " . $e->getMessage();
            return null;
        }
    }
}

// Classe de gestion des utilisateurs
class User {
    private $db;
    private $table = "Utilisateur";

    public function __construct($db) {
        $this->db = $db;
    }

    public function createUser($username, $email, $password) {
        // Vérifier si l'utilisateur existe déjà
        if ($this->isUserExists($username)) {
            echo "Un utilisateur avec le même nom existe déjà.";
            return false;
        }

        // Vérifier si l'e-mail est déjà utilisé
        if ($this->isEmailExists($email)) {
            echo "L'e-mail est déjà utilisé par un autre utilisateur.";
            return false;
        }

        // Vérifier la complexité du mot de passe
        if (!$this->isPasswordValid($password)) {
            echo "Le mot de passe doit contenir au moins 8 caractères, dont au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.";
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $conn = $this->db->getConnection();
            $conn->beginTransaction();

            $query = $conn->prepare("INSERT INTO $this->table (nom, email, mot_de_passe, date_inscription) VALUES (:nom, :email, :mot_de_passe, NOW())");
            $query->execute(array(
                "nom" => $username,
                "email" => $email,
                "mot_de_passe" => $hashedPassword
            ));

            $userId = $conn->lastInsertId();
            $token = bin2hex(random_bytes(32));

            $this->saveToken($conn, $userId, $token);
            $this->saveSession($conn, $userId, $token);

            $conn->commit();
            return $userId; // Retourner l'ID de l'utilisateur créé
        } catch (PDOException $e) {
            $conn->rollBack();
            echo "Erreur lors de la création de l'utilisateur : " . $e->getMessage();
            return false;
        }
    }

    public function loginUser($username, $password) {
        try {
            $conn = $this->db->getConnection();
            $query = $conn->prepare("SELECT id, nom, mot_de_passe FROM $this->table WHERE nom = :nom");
            $query->execute(array(
                "nom" => $username
            ));
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Vérifier le mot de passe en déchiffrant le mot de passe stocké et en le comparant avec le mot de passe fourni
                if (password_verify($password, $user['mot_de_passe'])) {
                    $token = bin2hex(random_bytes(32));

                    $this->saveToken($conn, $user['id'], $token);
                    $this->saveSession($conn, $user['id'], $token);

                    return $user['id']; // Retourner l'ID de l'utilisateur connecté
                } else {
                    echo "Mot de passe incorrect.";
                    return false;
                }
            } else {
                echo "Utilisateur non trouvé.";
                return false;
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la connexion de l'utilisateur : " . $e->getMessage();
            return false;
        }
    }

    public function resetPassword($email) {
        try {
            $conn = $this->db->getConnection();
            $query = $conn->prepare("SELECT id FROM $this->table WHERE email = :email");
            $query->execute(array(
                "email" => $email
            ));
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $userId = $user['id'];
                $token = bin2hex(random_bytes(32));

                $this->saveToken($conn, $userId, $token);

                // Envoyer un e-mail contenant le lien de réinitialisation du mot de passe

                return true; // Succès de la réinitialisation du mot de passe
            } else {
                echo "Aucun utilisateur avec cet e-mail n'a été trouvé.";
                return false;
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la réinitialisation du mot de passe : " . $e->getMessage();
            return false;
        }
    }

    private function isUserExists($username) {
        try {
            $conn = $this->db->getConnection();
            $query = $conn->prepare("SELECT COUNT(*) FROM $this->table WHERE nom = :nom");
            $query->execute(array(
                "nom" => $username
            ));
            $count = $query->fetchColumn();

            return $count > 0;
        } catch (PDOException $e) {
            echo "Erreur lors de la vérification de l'existence de l'utilisateur : " . $e->getMessage();
            return false;
        }
    }

    private function isEmailExists($email) {
        try {
            $conn = $this->db->getConnection();
            $query = $conn->prepare("SELECT COUNT(*) FROM $this->table WHERE email = :email");
            $query->execute(array(
                "email" => $email
            ));
            $count = $query->fetchColumn();

            return $count > 0;
        } catch (PDOException $e) {
            echo "Erreur lors de la vérification de l'existence de l'e-mail : " . $e->getMessage();
            return false;
        }
    }

    private function isPasswordValid($password) {
        // Au moins 8 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
        return preg_match($pattern, $password);
    }

    private function saveToken($conn, $userId, $token) {
        $tokenQuery = $conn->prepare("INSERT INTO Token (utilisateur_id, valeur) VALUES (:utilisateur_id, :valeur)");
        $tokenQuery->execute(array(
            "utilisateur_id" => $userId,
            "valeur" => $token
        ));
    }

    private function saveSession($conn, $userId, $token) {
        $expirationDate = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $sessionQuery = $conn->prepare("INSERT INTO Session (utilisateur_id, token, date_expiration) VALUES (:utilisateur_id, :token, :date_expiration)");
        $sessionQuery->execute(array(
            "utilisateur_id" => $userId,
            "token" => $token,
            "date_expiration" => $expirationDate
        ));
    }
}

// Utilisation des classes

// Démarrer la session
session_start();

// Vérifier si l'utilisateur est déjà connecté, le rediriger vers la page d'accueil
if (isset($_SESSION["utilisateur_id"])) {
    header("Location: index.php");
    exit;
}

// Vérifier si le formulaire d'inscription a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les valeurs des champs du formulaire
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Valider les données 

    // Créer une instance de la classe Database
    $db = new Database();

    // Créer une instance de la classe User
    $user = new User($db);

    // Créer l'utilisateur
    $userId = $user->createUser($username, $email, $password);

    if ($userId) {
        $_SESSION["utilisateur_id"] = $userId; // Stocker l'ID de l'utilisateur dans la session
        header("Location: index.php"); // Redirection vers la page d'accueil
        exit;
    } else {
        echo "Une erreur s'est produite lors de l'inscription.";
    }
}

// Vérifier si le formulaire de connexion a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    // Récupérer les valeurs des champs du formulaire
    $username = $_POST["username"];
    $password = $_POST["password"];

   
    // Créer une instance de la classe Database
    $db = new Database();

    // Créer une instance de la classe User
    $user = new User($db);

    // Connecter l'utilisateur
    $userId = $user->loginUser($username, $password);

    if ($userId) {
        $_SESSION["utilisateur_id"] = $userId; // Stocker l'ID de l'utilisateur dans la session
        header("Location: index.php"); // Redirection vers la page d'accueil
        exit;
    } else {
        echo "Une erreur s'est produite lors de la connexion.";
    }
}

// Vérifier si le formulaire de réinitialisation du mot de passe a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset_password"])) {
    // Récupérer la valeur du champ e-mail du formulaire
    $email = $_POST["email"];

    // Valider les données (vous pouvez ajouter des validations supplémentaires si nécessaire)

    // Créer une instance de la classe Database
    $db = new Database();

    // Créer une instance de la classe User
    $user = new User($db);

    // Réinitialiser le mot de passe
    $resetSuccess = $user->resetPassword($email);

    if ($resetSuccess) {
        echo "Un e-mail contenant les instructions de réinitialisation du mot de passe a été envoyé à votre adresse e-mail.";
    }
}
?>
