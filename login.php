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

    public function loginUser($username, $password) {
        try {
            $conn = $this->db->getConnection();
            $query = $conn->prepare("SELECT id, nom, mot_de_passe FROM $this->table WHERE nom = :nom");
            $query->execute(array(
                "nom" => $username
            ));
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Vérifier le mot de passe en déchiffrant le mot de passe stocké et en le comparant avec le mot de passe saisi
                if (password_verify($password, $user['mot_de_passe'])) {
                    return $user['id']; // Retourner l'ID de l'utilisateur
                } else {
                    return false; // Mot de passe incorrect
                }
            } else {
                return false; // Utilisateur non trouvé
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la connexion : " . $e->getMessage();
            return false;
        }
    }

    public function resetPassword($email) {
        // Vérifier si l'utilisateur existe
        if (!$this->isUserExistsByEmail($email)) {
            echo "Aucun utilisateur avec cet e-mail n'a été trouvé.";
            return false;
        }

        // Générer un nouveau mot de passe aléatoire
        $newPassword = $this->generateRandomPassword();

        // Hacher le nouveau mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        try {
            $conn = $this->db->getConnection();
            $query = $conn->prepare("UPDATE $this->table SET mot_de_passe = :mot_de_passe WHERE email = :email");
            $query->execute(array(
                "mot_de_passe" => $hashedPassword,
                "email" => $email
            ));

            // Envoyer le nouveau mot de passe par e-mail (code à implémenter)
            // ...

            return true;
        } catch (PDOException $e) {
            echo "Erreur lors de la réinitialisation du mot de passe : " . $e->getMessage();
            return false;
        }
    }

    private function isUserExistsByEmail($email) {
        try {
            $conn = $this->db->getConnection();
            $query = $conn->prepare("SELECT COUNT(*) FROM $this->table WHERE email = :email");
            $query->execute(array(
                "email" => $email
            ));
            $count = $query->fetchColumn();

            return $count > 0;
        } catch (PDOException $e) {
            echo "Erreur lors de la vérification de l'existence de l'utilisateur : " . $e->getMessage();
            return false;
        }
    }

    private function generateRandomPassword($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $password .= $characters[$index];
        }
        return $password;
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

// Vérifier si le formulaire de connexion a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les valeurs des champs du formulaire
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Valider les données 
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
        echo "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>
