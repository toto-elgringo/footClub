const headerToggleAddPlayer = document.querySelector(".header-toggle-add");
const headerButton = document.querySelector(".submit-button");
headerButton.addEventListener("click", () => {
    headerToggleAddPlayer.classList.toggle("active");
});