<?php
// Vérifier si l'utilisateur n'est pas connecté, le rediriger vers la page de connexion
session_start();
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: index.php");
    exit;
}

// Vérifier si l'ID de la tâche est spécifié
if (!isset($_GET["tache_id"])) {
    header("Location: taches.php");
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

// Récupérer les informations de la tâche à modifier
$tache_id = $_GET["tache_id"];
$requete = "SELECT * FROM taches WHERE id = :tache_id";
$statement = $connexion->prepare($requete);
$statement->bindParam(":tache_id", $tache_id);
$statement->execute();
$tache = $statement->fetch(PDO::FETCH_ASSOC);

// Vérifier si la tâche existe
if (!$tache) {
    header("Location: taches.php");
    exit;
}

// Traitement du formulaire de modification de tâche
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST["titre"];
    $description = $_POST["description"];
    $date_echeance = $_POST["date_echeance"];
    $id_categorie = $_POST["id_categorie"];
    $id_priorite = $_POST["id_priorite"];
    $completee = isset($_POST["completee"]) ? 1 : 0;

    // Mise à jour de la tâche dans la base de données
    $requete = "UPDATE taches SET titre = :titre, description = :description, date_echeance = :date_echeance, id_catégorie = :id_categorie, id_priorité = :id_priorite, complétée = :completee WHERE id = :tache_id";
    $statement = $connexion->prepare($requete);
    $statement->bindParam(":titre", $titre);
    $statement->bindParam(":description", $description);
    $statement->bindParam(":date_echeance", $date_echeance);
    $statement->bindParam(":id_categorie", $id_categorie);
    $statement->bindParam(":id_priorite", $id_priorite);
    $statement->bindParam(":completee", $completee);
    $statement->bindParam(":tache_id", $tache_id);

    try {
        $statement->execute();
        header("Location: modifier_tache.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de la modification de la tâche : " . $e->getMessage();
    }
}

// Récupération des catégories et priorités depuis la base de données
$categories = $connexion->query("SELECT * FROM Catégorie")->fetchAll(PDO::FETCH_ASSOC);
$priorites = $connexion->query("SELECT * FROM Priorité")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier une tâche</title>
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

        .delete-button {
            color: #dc3545;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Modifier la tâche :</h3>
        <form method="POST" action="modifier_tache.php">
            <div class="form-group">
                <label for="titre">Titre :</label>
                <input type="text" id="titre" name="titre" class="form-control" value="<?php echo $tache['titre']; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description :</label>
                <textarea id="description" name="description" class="form-control" required><?php echo $tache['description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="date_echeance">Date d'échéance :</label>
                <input type="date" id="date_echeance" name="date_echeance" class="form-control" value="<?php echo $tache['date_echeance']; ?>" required>
            </div>
            <div class="form-group">
                <label for="id_categorie">Catégorie :</label>
                <select id="id_categorie" name="id_categorie" class="form-control" required>
                    <?php foreach ($categories as $categorie) : ?>
                        <option value="<?php echo $categorie['id']; ?>" <?php echo ($tache['id_catégorie'] == $categorie['id']) ? 'selected' : ''; ?>><?php echo $categorie['nom']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_priorite">Priorité :</label>
                <select id="id_priorite" name="id_priorite" class="form-control" required>
                    <?php foreach ($priorites as $priorite) : ?>
                        <option value="<?php echo $priorite['id']; ?>" <?php echo ($tache['id_priorité'] == $priorite['id']) ? 'selected' : ''; ?>><?php echo $priorite['nom']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="completee">Complétée :</label>
                <input type="checkbox" id="completee" name="completee" <?php echo ($tache['complétée']) ? 'checked' : ''; ?>>
            </div>
            <button type="submit" class="btn btn-primary">Modifier</button>
        </form>

        <div class="mt-4">
            <h4>Supprimer la tâche :</h4>
            <p>Êtes-vous sûr de vouloir supprimer cette tâche ?</p>
            <button id="deleteButton" class="btn btn-danger delete-button"><i class="fas fa-trash"></i> Supprimer</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById("deleteButton").addEventListener("click", function() {
            if (confirm("Êtes-vous sûr de vouloir supprimer cette tâche ?")) {
                // Rediriger vers la page de suppression avec l'ID de la tâche
                window.location.href = "supprimer_tache.php?tache_id=<?php echo $tache['id']; ?>";
            }
        });
    </script>
</body>
</html>
