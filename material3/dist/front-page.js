import '@material/web/all.js';
import { styles as typescaleStyles } from '@material/web/typography/md-typescale-styles.js';

document.adoptedStyleSheets.push(typescaleStyles.styleSheet);

document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.app-card');
    cards.forEach(card => {
        card.addEventListener('click', () => {
            const link = card.getAttribute('data-href');
            if(link){
                window.location.href = link;
            }
        });
    });

    const goButton = document.querySelector('.go-button');
    if(goButton){
        const link = goButton.getAttribute('data-link');
        goButton.addEventListener('click', () => {
            if(link){
                window.location.href = link;
            }
        });
    }
});
