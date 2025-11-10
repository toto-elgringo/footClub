document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé pour la page joueursUpdate');

    // gestion de la suppression des équipes
    const deleteTeamButtons = document.querySelectorAll('.delete-team');
    console.log('Nombre de boutons delete-team trouvés:', deleteTeamButtons.length);

    deleteTeamButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const bubble = this.closest('.equipe-bubble');
            const deleteForm = bubble.querySelector('.delete-team-form');
            if (confirm('Êtes-vous sûr de vouloir retirer ce joueur de cette équipe ?')) {
                deleteForm.submit();
            }
        });
    });
});
