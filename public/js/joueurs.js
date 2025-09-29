function filterPlayers() {
    const searchInput = document.getElementById('search');
    const cards = document.querySelectorAll('.player-card');
    const searchTerm = searchInput.value.toLowerCase().trim();
    
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

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé, initialisation du filtre...');
    
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.value = ''; 
        console.log('Champ de recherche vidé');
    }
    
    const cards = document.querySelectorAll('.player-card');
    cards.forEach(card => {
        card.style.display = 'block';
    });
    console.log('Toutes les cartes affichées initialement');
    
    const filterButton = document.getElementById('filter-button');
    if (filterButton) {
        filterButton.addEventListener('click', filterPlayers);
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterPlayers();
        });
    }
});