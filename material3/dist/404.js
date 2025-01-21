document.addEventListener('DOMContentLoaded', () => {
  const goBackButton = document.querySelector('.go-back-button');
  
  if (goBackButton) {
    goBackButton.addEventListener('click', () => {
      window.history.back();
    });
  }
});