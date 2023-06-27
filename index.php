<?php
// Vérifier si l'utilisateur n'est pas connecté, le rediriger vers la page de connexion
session_start();
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accueil</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #333;
        }

        .navbar-brand,
        .nav-link {
            color: #fff;
        }

        .navbar-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .clock {
            font-size: 18px;
            margin-right: 10px;
        }

        .date {
            font-size: 14px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .jumbotron {
            background-color: #333;
            color: #fff;
            padding: 40px;
        }

        .welcome-message {
            font-size: 24px;
            margin-bottom: 20px;
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

        .relaxation-video {
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 56.25%; /* Ratio d'aspect 16:9 (hauteur / largeur) */
            height: 0;
            overflow: hidden;
        }

        .relaxation-video iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .image-slider {
            margin-bottom: 20px;
        }

        .carousel-item img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="#">
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

    <div class="jumbotron">
        <h2 class="welcome-message">Bienvenue</h2>
        <p>Voici comment fonctionne notre site :</p>
        <ul>
            <li>Personnalisez votre profil</li>
            <li>Consultez votre historique</li>
            <li>Insérez de nouvelles informations</li>
        </ul>
        <div class="relaxation-video">
            <h4>Profitez d'une vidéo de relaxation :</h4>
            <iframe width="100%" height="100%" src="https://www.youtube.com/embed/MWkIxYtB8Ag?start=17" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>

        <div class="image-slider">
            <h4>Découvrez ces magnifiques images :</h4>
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="s21-planning.png" class="d-block w-100" alt="Image 1">
                    </div>
                    <div class="carousel-item">
                        <img src="R.png" class="d-block w-100" alt="Image 2">
                    </div>
                    <div class="carousel-item">
                        <img src="Schedule-Planning-1.png" class="d-block w-100" alt="Image 3">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <h3 class="title">Vos tâches :</h3>
        <ul class="task-list">
            <?php foreach ($taches as $tache) : ?>
                <li class="task-list-item">
                    <div class="task-title"><?php echo $tache['titre']; ?></div>
                    <div class="task-status"><?php echo $tache['complétée'] ? 'Terminée' : 'En cours'; ?></div>
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
