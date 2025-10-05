// Toggle menue
const headerToggleAddPlayer = document.querySelector(".header-toggle-add");
const headerButton = document.querySelector(".submit-button");
headerButton.addEventListener("click", () => {
  headerToggleAddPlayer.classList.toggle("active");
});








document.addEventListener('click', function (e) {
  // 1️⃣ Vérifie si on clique sur un bouton delete
  if (e.target && e.target.matches('.delete')) {
      // 2️⃣ Remonte jusqu'à la carte la plus proche (player-card, team-card, etc.)
      const card = e.target.closest('[data-type]');
      if (!card) return;

      // 3️⃣ Récupère le type et l'id de la carte (ex: player / team / staff)
      const type = card.dataset.type || 'élément';
      const id = card.dataset.id || '?';

      // 4️⃣ Essaie de récupérer le nom à afficher dans le confirm
      const nameElement = card.querySelector('#player-name, #team-name, #staff-name');
      const name = nameElement ? nameElement.textContent.trim() : `${type} #${id}`;

      // 5️⃣ Demande confirmation avant suppression
      if (confirm(`Supprimer ${name} (${type}) ? Cette action est définitive.`)) {
          // 6️⃣ Trouve et soumet le formulaire de suppression
          const form = card.querySelector('form.delete-form');
          if (form) form.submit();
          else alert("Formulaire de suppression introuvable !");
      }
  }
});
