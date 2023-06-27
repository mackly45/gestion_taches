<?php
// Vérifier si l'utilisateur n'est pas connecté, le rediriger vers la page de connexion
session_start();
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: index.php");
    exit;
}

// Connexion à la base de données (remplacez les informations de connexion selon votre configuration)
$dsn = "mysql:host=localhost;dbname=taches_projet";
$username = "root";
$password = "";

try {
    $connexion = new PDO($dsn, $username, $password);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    exit;
}

// Traitement de la recherche par mots-clés
if (isset($_GET["recherche"])) {
    $recherche = $_GET["recherche"];
    $requete = "SELECT * FROM taches WHERE (titre LIKE :recherche OR description LIKE :recherche)";
    $statement = $connexion->prepare($requete);
    $statement->bindValue(":recherche", "%$recherche%");
} else {
    $requete = "SELECT * FROM taches WHERE complétée = true";
    $statement = $connexion->prepare($requete);
}

try {
    $statement->execute();
    $taches = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des tâches : " . $e->getMessage();
}

// Traitement de la modification de tâche
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["modifier"])) {
    $tache_id = $_POST["tache_id"];
    $titre = $_POST["titre"];
    $description = $_POST["description"];

    $requete = "UPDATE taches SET titre = :titre, description = :description WHERE id = :tache_id";
    $statement = $connexion->prepare($requete);
    $statement->bindParam(":titre", $titre);
    $statement->bindParam(":description", $description);
    $statement->bindParam(":tache_id", $tache_id);

    try {
        $statement->execute();
        header("Location: historique.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de la modification de la taches : " . $e->getMessage();
    }
}

// Traitement de la suppression de tâche
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["supprimer"])) {
    $tache_id = $_POST["tache_id"];

    $requete = "DELETE FROM taches WHERE id = :tache_id";
    $statement = $connexion->prepare($requete);
    $statement->bindParam(":tache_id", $tache_id);

    try {
        $statement->execute();
        header("Location: historique.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression de la taches : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Historique des tâches</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
        }

        .btn {
            margin-top: 20px;
        }

        .task-list {
            list-style: none;
            padding: 0;
        }

        .task-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        .task-list-item .task-title {
            flex-grow: 1;
            margin-right: 10px;
        }

        .task-list-item .task-actions {
            display: flex;
            align-items: center;
        }

        .task-list-item .task-actions .btn {
            margin-left: 5px;
        }

        .search-form {
            margin-bottom: 20px;
        }

        .search-form input {
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Historique des tâches :</h3>

        <form class="search-form" method="GET">
            <div class="input-group">
                <input type="text" class="form-control" name="recherche" placeholder="Rechercher par mots-clés">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i> Rechercher</button>
                </div>
            </div>
        </form>

        <ul class="task-list">
            <?php foreach ($taches as $tache) : ?>
                <li class="task-list-item">
                    <div class="task-title"><?php echo $tache['titre']; ?></div>
                    <div class="task-actions">
                        <form method="POST" action="modifier_tache.php">
                            <input type="hidden" name="tache_id" value="<?php echo $tache['id']; ?>">
                            <button type="submit" class="btn btn-primary" name="modifier"><i class="fas fa-edit"></i> Modifier</button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="tache_id" value="<?php echo $tache['id']; ?>">
                            <button type="submit" class="btn btn-danger" name="supprimer"><i class="fas fa-trash"></i> Supprimer</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <p><a href="index.php">Retour à la liste des tâches</a></p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
