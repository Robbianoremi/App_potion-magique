  <?php
  session_start(); // Démarrage de la session
  $title = 'Profil'; // Déclaration du titre de la page
  require '../../core/functions.php'; // Inclusion du fichier de fonctions
  $user = $_SESSION['profil']; // Récupération des données de l'utilisateur
  $roles = $_SESSION['profil']['roles'];  // Récupération des rôles de l'utilisateur
  logedIn(); // Appel de la fonction de connexion
  include '../partials/head.php'; // Inclusion du fichier d'en-tête
  include '../partials/menu.php'; // Inclusion du fichier de navigation
 ?>
<div class="container">
  <?php displayMessage(); ?>
    <h1 class="mt-3">Bonjour <?= $user['nom'] ?></h1>
    <pre>id user : <?= $user['idutilisateur']?></pre>
    <p>Mon email: <?= $user['email'] ?></p>
    <p class="fs-3">Mes rôles : </br>
  <?php foreach($roles as $role): ?>
      <p><?= $role ?></p>
    <?php endforeach; ?>
  </p>
    
    <a class="btn btn-danger" href="../controllers/logout.php">déconnexion</a>
</div>
    
<?php
  include '../partials/footer.php'; // Inclusion du fichier de pied de page