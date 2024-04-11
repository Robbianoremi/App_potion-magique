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
  <div class="container-fluid my-5">
    <div class="row ">
      <div class="col-6 m-auto  ">

        <div class="card bg-light rounded shadow ">
          <div class="card-header">
            Mon profil
          </div>

          <div class="row g-0">
            <div class="col-md-4">
              <img src="../assets/images/francois.png" class="img-fluid rounded" alt="...">
            </div>
            <div class="col-md-8">

              <div class="card-body ps-5">
                <?php displayMessage(); ?>
                <h1 class="mt-3">Bonjour <?= $user['nom'] ?></h1>
                <div class="card-text ">
                  <pre>id user : <?= $user['idutilisateur'] ?></pre>
                  <p>Mon email: <?= $user['email'] ?></p>
                  <p class="fs-3">Mes rôles : </br>
                    <?php foreach ($roles as $role) : ?>
                  <p><?= $role ?></p>
                <?php endforeach; ?>
                </p>
                </div>
                <a class="btn btn-danger" href="../controllers/logout.php">déconnexion</a>

              </div>
            </div>
          </div>
        </div>




      </div>


    </div>
  </div>


  <div class="container-fluid my-5 ">
    <div class="row ">
      <div class="col-lg-5 col-md-6 col-10 mx-auto bg-light border border-1 rounded shadow p-3">
        <div class="effets">
          <h2 class="fs-3 mb-4">Liste des effets</h2>
          <?php displayeffets(); ?> <!-- Appel de la fonction pour afficher les ingredients -->
        </div>
        <h3 class="mt-5 mb-4 text-center text-decoration-underline">Ajout d'effets</h3>
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
            <button type="submit" name="submit" class="btn btn-primary px-3 py-2">Ajouter</button>
          </div>
        </form>
      </div>
      <div class="col-lg-5 col-md-6 col-10 mx-auto bg-light border border-1 rounded shadow p-3">
        <div class="ingredients mb-2">
          <h2 class="fs-3 mb-4">Liste des ingrédients</h2>
          <?php displayingredients(); ?> <!-- Appel de la fonction pour afficher les ingredients -->
        </div>
        <h3 class="mt-5 mb-4 text-center text-decoration-underline ">Ajout d'un ingredients</h3>
        <form method="POST" action="../controllers/addIngredient.php">
          <div class="mb-3">
            <label for="name" class="form-label fw-bold">Nom</label>
            <input type="text" name="name" id="name" class="form-control">
          </div>
          <div class="mb-3">
            <label for="propriete" class="form-label fw-bold">Propriete</label>
            <input type="text" name="propriete" id="propriete" class="form-control">
          </div>
          <div class="mb-4">

            <?php typeIngredient(); ?>

          </div>
          <div class="mb-4">

            <?php rareteIngredient(); ?>

          </div>
          <div class="text-center">

            <button type="submit" name="submit" class="btn btn-primary px-3 py-2">Ajouter</button>

          </div>
        </form>
      </div>

    </div>
    <div class="row ">
      <div class="col-6 m-auto">
        <div class="card bg-light border border-1 rounded shadow mt-3">
          <div class="card-body">
            <div class="ingredients mb-2">
              <div class="potion">
                <h2 class="fs-3">Liste des potions</h2>
                <?php displayPotion(); ?> <!-- Appel de la fonction pour afficher les potions -->
              </div>
              <h3 class="text-center h2 py-3">Ajouter une nouvelle potion</h3>
              <form action="../controllers/addpotion.php" method="post">
                <div class="mb-3">
                  <label for="nomPotion" class="form-label">Nom de la potion :</label>
                  <input type="text" class="form-control" id="nomPotion" name="nomPotion" required>
                </div>
                <div class="mb-3">
                  <label for="niveauMagie" class="form-label">Niveau de magie requis</label>
                  <select name="niveauMagie" id="niveauMagie">
                    <?php displayOptionNiveauMagie(); ?>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="descriptionPotion" class="form-label">Description :</label>
                  <textarea class="form-control" id="descriptionPotion" name="descriptionPotion" required></textarea>
                </div>
                <div class="mb-3">
                  <label for="dureePotion" class="form-label">Temps de Préparation :</label>
                  <input type="number" class="form-control" id="dureePotion" name="dureePotion" required>
                  <label for="uniteDureePotion" class="form-label mt-3">unité de temps</label>
                  <select name="uniteDureePotion" id="uniteDureePotion">
                    <option value="Minutes">Minutes</option>
                    <option value="Heures">Heures</option>
                  </select>
                </div>
                <div id="effetsExistants" class="mb-3">
                  <div class="effetsExistant mb-3">
                    <label for="effetsExistant" class="form-label h4">Ajouter un effet</label>
                    <select name="effetsExistant[]" class="form-select">
                      <?php displayOptionEffets(); ?>
                      <!-- Les options doivent être générées dynamiquement à partir de la base de données -->
                    </select>
                  </div>
                </div>
                <button type="button" class="btn btn-secondary mb-3 fw-bold" onclick="addEffetExistant()"> + effet</button><br>
                <div id="ingredientsExistants" class="mb-3">
                  <div class="ingredientExistant mb-3">
                    <label for="ingredientExistant" class="form-label h4">Ajouter Ingrédient</label>
                    <select name="ingredientsExistant[]" class="form-select">
                      <?php displayOptionIngredients(); ?>
                      <!-- Les options doivent être générées dynamiquement à partir de la base de données -->
                    </select>

                    <label for="quantiteExistant" class="form-label">Quantité:</label>
                    <input type="number" name="quantiteExistant[]" class="form-control">

                    <label for="uniteMesureExistant" class="form-label">Unité de mesure:</label>
                    <select name="uniteMesureExistant[]" class="form-select">
                      <?php displayOptionUniteMesure(); ?>
                      <!-- Les unités de mesure doivent être générées dynamiquement à partir de la base de données -->
                    </select>
                  </div>
                </div>

                <button type="button" class="btn btn-secondary mb-3 fw-bold" onclick="addIngredientExistant()"> + ingrédient</button><br>

                <div id="etapesPreparation" class="mb-3">
                  <h4>Etape de préparation</h4>
                  <div class="etapePreparation mb-3">

                    <label for="numeroEtape" class="form-label">Étape:</label>
                    <span class="numeroEtape">1</span>
                    <input type="hidden" name="numeroEtape[]" value="1">

                    <label for="descriptionEtape" class="form-label">Description de l'étape:</label>
                    <textarea name="descriptionEtape[]" class="form-control" required></textarea>

                    <label for="dureeEstimee" class="form-label">Durée estimée:</label>
                    <input type="number" class="form-control" name="dureeEstimee[]" class="form-control" required>

                    <label for="uniteDureeEstimee" class="form-label">unité de temps</label>
                    <select name="uniteDureeEstimee[]" id="uniteDureeEstimee">
                      <option value="Minutes">Minutes</option>
                      <option value="Heures">Heures</option>
                    </select>
                  </div>

                </div>
                <button type="button" class="btn btn-secondary mb-3 fw-bold" onclick="addEtapePreparation()">+ étape</button><br>

                <button type="submit" class="btn btn-primary fw-bold" name="submit">🪄 Ajouter la potion 🧪</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
    include '../partials/footer.php'; // Inclusion du fichier de pied de page