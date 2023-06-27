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

// Traitement du formulaire d'ajout de tâche
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST["titre"];
    $description = $_POST["description"];
    $date_echeance = $_POST["date_echeance"];
    $id_categorie = $_POST["id_categorie"];
    $id_priorite = $_POST["id_priorite"];
    $utilisateur_id = $_SESSION["utilisateur_id"];

    // Insertion de la nouvelle tâche dans la base de données
    $requete = "INSERT INTO taches (titre, description, date_echeance, id_catégorie, id_priorité, utilisateur_id) 
                VALUES (:titre, :description, :date_echeance, :id_categorie, :id_priorite, :utilisateur_id)";
    $statement = $connexion->prepare($requete);
    $statement->bindParam(":titre", $titre);
    $statement->bindParam(":description", $description);
    $statement->bindParam(":date_echeance", $date_echeance);
    $statement->bindParam(":id_categorie", $id_categorie);
    $statement->bindParam(":id_priorite", $id_priorite);
    $statement->bindParam(":utilisateur_id", $utilisateur_id);

    try {
        $statement->execute();
        header("Location: taches.php");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout de la tâche : " . $e->getMessage();
    }
}

// Récupération des catégories et priorités depuis la base de données
$categories = $connexion->query("SELECT * FROM Catégorie")->fetchAll(PDO::FETCH_ASSOC);
$priorites = $connexion->query("SELECT * FROM Priorité")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tâches</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

        .task-list-item .task-status {
            color: #4db8ff;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="index.php">
            <img src="logo.png" width="30" height="30" class="d-inline-block align-top" alt="Logo">
            Mon Site
        </a>
        <div class="navbar-center">
            <span class="clock" id="clock"></span>
            <span class="date" id="date"></span>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="taches.php">Tâches</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="historique.php">Historique</a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- Fin Navbar -->

    <div class="container">
        <h3>Ajouter une nouvelle tâche :</h3>
        <form method="POST">
            <div class="form-group">
                <label for="titre">Titre :</label>
                <input type="text" id="titre" name="titre" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Description :</label>
                <textarea id="description" name="description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="date_echeance">Date d'échéance :</label>
                <input type="date" id="date_echeance" name="date_echeance" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="id_categorie">Catégorie :</label>
                <select id="id_categorie" name="id_categorie" class="form-control" required>
                    <?php foreach ($categories as $categorie) : ?>
                        <option value="<?php echo $categorie['id']; ?>"><?php echo $categorie['nom']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_priorite">Priorité :</label>
                <select id="id_priorite" name="id_priorite" class="form-control" required>
                    <?php foreach ($priorites as $priorite) : ?>
                        <option value="<?php echo $priorite['id']; ?>"><?php echo $priorite['nom']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>

        <h3>Vos tâches :</h3>
        <ul class="task-list">
            <?php
            $utilisateur_id = $_SESSION["utilisateur_id"];
            $requete = "SELECT * FROM taches WHERE utilisateur_id = :utilisateur_id";
            $statement = $connexion->prepare($requete);
            $statement->bindParam(":utilisateur_id", $utilisateur_id);
            $statement->execute();
            $taches = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($taches as $tache) :
            ?>
                <li class="task-list-item">
                    <div class="task-title"><?php echo $tache['titre']; ?></div>
                    <div class="task-status"><?php echo $tache['complétée'] ? '<i class="fas fa-check-circle"></i> Terminée' : '<i class="fas fa-circle-notch"></i> En cours'; ?></div>
                </li>
            <?php endforeach; ?>
        </ul>

        <p><a href="logout.php">Se déconnecter</a></p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Obtenir l'heure actuelle
        function getCurrentTime() {
            var date = new Date();
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var seconds = date.getSeconds();

            hours = formatTime(hours);
            minutes = formatTime(minutes);
            seconds = formatTime(seconds);

            var timeString = hours + ":" + minutes + ":" + seconds;
            return timeString;
        }

        // Obtenir la date actuelle
        function getCurrentDate() {
            var date = new Date();
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var day = date.getDate();

            month = formatTime(month);
            day = formatTime(day);

            var dateString = day + "/" + month + "/" + year;
            return dateString;
        }

        // Ajouter un zéro en tête si la valeur est inférieure à 10
        function formatTime(time) {
            if (time < 10) {
                time = "0" + time;
            }
            return time;
        }

        // Mettre à jour l'horloge et la date toutes les secondes
        function updateClock() {
            var clockElement = document.getElementById("clock");
            var dateElement = document.getElementById("date");

            clockElement.textContent = getCurrentTime();
            dateElement.textContent = getCurrentDate();

            setTimeout(updateClock, 1000);
        }

        // Démarrer la mise à jour de l'horloge
        updateClock();
    </script>
</body>
</html>
