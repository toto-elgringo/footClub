function filterPlayers() {
    const searchInput = document.getElementById('search');
    const cards = document.querySelectorAll('.player-card');
    const searchTerm = searchInput.value.toLowerCase().trim();
    
    // Débogage
    console.log('Filtrage des joueurs avec le terme:', searchTerm);
    console.log('Nombre de cartes trouvées:', cards.length);
    
    cards.forEach(card => {
        const name = card.querySelector('h2').textContent.toLowerCase();
        if (name.includes(searchTerm) || searchTerm === '') {
            card.style.display = 'block';
            console.log('Afficher la carte:', name);
        } else {
            card.style.display = 'none';
            console.log('Cacher la carte:', name);
        }
    });
}

// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé, initialisation du filtre...');
    
    // S'assurer que le champ de recherche est vide au chargement
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.value = ''; // Vider le champ de recherche
        console.log('Champ de recherche vidé');
    }
    
    // Afficher toutes les cartes initialement
    const cards = document.querySelectorAll('.player-card');
    cards.forEach(card => {
        card.style.display = 'block';
    });
    console.log('Toutes les cartes affichées initialement');
    
    // Ajouter l'écouteur d'événement sur le bouton de filtre
    const filterButton = document.getElementById('filter-button');
    if (filterButton) {
        filterButton.addEventListener('click', filterPlayers);
        console.log('Écouteur d\'événement ajouté au bouton de filtre');
    }
    
    // Ajouter l'écouteur d'événement sur le champ de recherche
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            console.log('Changement dans le champ de recherche');
            filterPlayers();
        });
    }
});