function addEffetExistant() {
    let idEffet = document.querySelector('#effetsExistants');
    console.log(idEffet);    
    let cloneDivEffet = idEffet.querySelector('.effetsExistant').cloneNode(true);

    let deleteButton = document.createElement('button');
    deleteButton.textContent = 'Supprimer';
    deleteButton.classList.add('btn', 'btn-danger', 'my-2');

    deleteButton.addEventListener('click', function() {
        cloneDivEffet.remove();
    });

    cloneDivEffet.appendChild(deleteButton);
    idEffet.appendChild(cloneDivEffet);
}
function addIngredientExistant() {
    let idIngredient = document.querySelector('#ingredientsExistants');
    let cloneDivIngredient = idIngredient.querySelector('.ingredientExistant').cloneNode(true);

    let deleteButton = document.createElement('button');
    deleteButton.textContent = 'Supprimer';
    deleteButton.classList.add('btn', 'btn-danger', 'my-2');

    deleteButton.addEventListener('click', function() {
        cloneDivIngredient.remove();
    });

    cloneDivIngredient.appendChild(deleteButton);
    idIngredient.appendChild(cloneDivIngredient);
}
function addEtapePreparation() {
    let idEtape = document.querySelector('#etapesPreparation');
    let cloneDivEtape = idEtape.querySelector('.etapePreparation').cloneNode(true);

    let existingEtapes = idEtape.querySelectorAll('.etapePreparation').length;
    let newNumeroEtape = existingEtapes + 1;
    cloneDivEtape.querySelector('.numeroEtape').textContent = newNumeroEtape;
    cloneDivEtape.querySelector('input[name="numeroEtape[]"]').value = newNumeroEtape;

    let deleteButton = document.createElement('button');
    deleteButton.textContent = 'Supprimer';
    deleteButton.classList.add('btn', 'btn-danger', 'my-2');

    deleteButton.addEventListener('click', function() {
        cloneDivEtape.remove();
    });

    cloneDivEtape.appendChild(deleteButton);
    idEtape.appendChild(cloneDivEtape);
}