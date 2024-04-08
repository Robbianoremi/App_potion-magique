  <?php
  session_start(); // Démarrage de la session
  $title = 'Profil'; // Déclaration du titre de la page
  require '../../core/functions.php'; // Inclusion du fichier de fonctions
  require '../../config/db.php';
  $user = $_SESSION['profil']; // Récupération des données de l'utilisateur
  $roles = $_SESSION['profil']['roles'];  // Récupération des rôles de l'utilisateur
  logedIn(); // Appel de la fonction de connexion
  include '../partials/head.php'; // Inclusion du fichier d'en-tête
  include '../partials/menu.php'; // Inclusion du fichier de navigation
  ?>
  <div class="container">
    <?php displayMessage(); ?>
    <h1 class="mt-3">Bonjour <?= $user['nom'] ?></h1>
    <pre>id user : <?= $user['idutilisateur'] ?></pre>
    <p>Mon email: <?= $user['email'] ?></p>
    <p class="fs-3">Mes rôles : </br>
      <?php foreach ($roles as $role) : ?>
    <p><?= $role ?></p>
  <?php endforeach; ?>
  </p>

  <a class="btn btn-danger" href="../controllers/logout.php">déconnexion</a>
  </div>

  <div class="container">
    <div class="row pt-5 my-5 p-2">
      <div class="col-4 m-auto  list p-3 mt-3 border-bottom border-top border-success-subtle border-5">
        <h2 class="fs-3 mb-4">Liste des ingrédients</h2>
        <div class="ingredients mb-2">
          <?php displayingredients(); ?> <!-- Appel de la fonction pour afficher les ingredients -->
        </div>
        
      </div>
      <div class="col-4 m-auto list p-3 mt-3 border-bottom border-top border-success-subtle border-5">
        <div class="effets">
          <h2 class="fs-3 mb-4">Liste des effets</h2>
          <?php displayeffets(); ?> <!-- Appel de la fonction pour afficher les ingredients -->
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-lg-5 col-md-6 col-10 mx-auto bg-light border border-1 rounded shadow p-3">
        <h3 class="mt-3 mb-4 text-center">Ajout d'effets</h3>
        <form method="POST" action="../controllers/addEffet.php">
          <div class="mb-3">
            <label for="name" class="form-label fw-bold">Nom</label>
            <input type="text" name="name" id="name" class="form-control">
          </div>
          <div class="mb-3">
            <label for="description" class="form-label fw-bold">Description</label>
            <input type="description" name="description" id="Description" class="form-control">
          </div>
          <div class="mb-4">
            <label for="duree" class="form-label fw-bold">Durée</label>
            <input type="duree" name="duree" id="duree" class="form-control">
          </div>
          <div class="text-center">
            <button type="submit" name="submit" class="btn btn-success px-3 py-2">Ajouter</button>
          </div>
        </form>
      </div>
      <div class="col-lg-5 col-md-6 col-10 mx-auto bg-light border border-1 rounded shadow p-3">
        <h3 class="mt-3 mb-4 text-center">Ajout d'un ingredients</h3>
        <form method="POST" action="../controllers/addIngredient.php">
          <div class="mb-3">
            <label for="name" class="form-label fw-bold">Nom</label>
            <input type="text" name="name" id="name" class="form-control">
          </div>
          <div class="mb-3">
            <label for="propriete" class="form-label fw-bold">propriete</label>
            <input type="text" name="propriete" id="propriete" class="form-control">
          </div>
          <div class="mb-4">
           
           <?php typeIngredient();?>

          </div>
          <div class="text-center">
            <button type="submit" name="submit" class="btn btn-success px-3 py-2">Ajouter</button>
          </div>
        </form>
      </div>

    </div>
  </div>
  <?php
  include '../partials/footer.php'; // Inclusion du fichier de pied de page