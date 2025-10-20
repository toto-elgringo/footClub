// toggle menue
const headerToggleAddPlayer = document.querySelector(".header-toggle-add");
const headerButton = document.querySelector(".submit-button");
headerButton.addEventListener("click", () => {
  headerToggleAddPlayer.classList.toggle("active");
});







// bouton de suppression
document.addEventListener('click', function (e) {
  if (e.target && e.target.matches('.delete')) {
      const card = e.target.closest('[data-type]');
      if (!card) return;
      const type = card.dataset.type || 'élément';
      const id = card.dataset.id || '?';

      const nameElement = card.querySelector('#player-name, #team-name, #staff-name');
      const name = nameElement ? nameElement.textContent.trim() : `${type} #${id}`;

      if (confirm(`Supprimer ${name} (${type}) ? Cette action est définitive.`)) {
          const form = card.querySelector('form.delete-form');
          if (form) form.submit();
          else alert("Formulaire de suppression introuvable !");
      }
  }
});
