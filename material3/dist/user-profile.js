import '@material/web/all.js';
import { styles as typescaleStyles } from '@material/web/typography/md-typescale-styles.js';
document.adoptedStyleSheets.push(typescaleStyles.styleSheet);

document.addEventListener('DOMContentLoaded', () => {
  const appCards = document.querySelectorAll('.app-card');
  appCards.forEach(card => {
    card.addEventListener('click', () => {
      const userId = userProfileData.userId;
      const userProfileUrl = userProfileData.userProfileUrl;
      const appLink = card.querySelector('a')?.href || card.querySelector('h2')?.innerText;
      window.location.href = card.querySelector('a')?.href || card.querySelector('.app-title').innerText;
    });
  });
});
