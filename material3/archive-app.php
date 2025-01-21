<?php

/**
 * Ce fichier affiche une liste d'applications et permet de les filtrer dynamiquement par langue 
 * grâce à une requête AJAX. Il inclut également une fonctionnalité de tri alphabétique des applications.
 * 
 * Fonctionnalités principales :
 * - Affichage des applications avec leurs icônes, titres et langages associés.
 * - Filtrage des applications par langage via une boîte de dialogue.
 * - Tri des applications de A à Z sans recharger la page grâce à AJAX.
 * - Utilisation de composants Material Design pour l'interface utilisateur.
 */

/**
 * Template Name: AppMage - List Apps (AJAX Filter)
 * Template Post Type: page
 */
get_header();
?>
<script type="module">
  import '@material/web/all.js';
  import {styles as typescaleStyles} from '@material/web/typography/md-typescale-styles.js';
  document.adoptedStyleSheets.push(typescaleStyles.styleSheet);
  window.reInitMaterialElements = () => {
    console.log('reInitMaterialElements()');
  };
</script>

<style>
  body {
    margin: 0; 
    padding: 0;
    background-color: #FCE4EC; 
    font-family: 'Roboto', sans-serif;
  }

  .appmage-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
  }

  .appmage-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
  }
  .appmage-header h1 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #4A148C;
    margin: 0;
  }

  .controls {
    display: flex;
    gap: 1rem;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
  }

  md-filled-button#sortAZ {
    --md-sys-color-primary: #C2185B;
    color: #FFF;
  }

  md-filled-button#openLangDialog {
    --md-sys-color-primary: #AD1457;
    color: #FFF;
  }

  .apps-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
  }

  md-outlined-card.app-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #FFEBEE;      
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border-radius: 12px;
    padding: 1rem;
    cursor: pointer;
    transition: transform 0.2s ease;
    --md-sys-color-outline: #F48FB1;
  }
  md-outlined-card.app-card:hover {
    transform: translateY(-2px);
  }

  .app-icon {
    width: 64px; 
    height: 64px;
    border-radius: 8px;
    object-fit: cover;
    margin-bottom: 0.5rem;
  }
  .app-title {
    margin: 0.25rem 0;
    font-size: 1rem;
    color: #4A148C;
  }
  .lang-tag {
    display: inline-block;
    font-size: 0.75rem;
    background: #F48FB1;
    color: #880E4F;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    margin-top: 0.25rem;
    font-weight: 500;
  }

  .loading-overlay {
    display: none;
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999;
  }
  .loading-overlay md-circular-progress {
    --md-circular-progress-size: 48px;
    --md-circular-progress-active-indicator-width: 8;
    --md-circular-progress-color: #EC407A; 
  }

  .bottom-spacing {
    height: 70px;
  }

  md-dialog#langDialog {
   
  }

  .lang-radio-container {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin: 1rem 0;
  }
  .lang-radio-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .lang-radio-label .fa-check-circle {
    color: #EC407A;
  }
</style>

<div class="loading-overlay" id="ajaxLoader">
  <md-circular-progress indeterminate></md-circular-progress>
</div>

<div class="appmage-wrapper">
  <div class="appmage-header">
    <h1>All Apps</h1>
    <div class="controls">
      <md-filled-button id="openLangDialog">Filter by Language</md-filled-button>

      <md-filled-button id="sortAZ" title="Sort the displayed apps from A to Z">
        Sort A–Z
      </md-filled-button>
    </div>
  </div>

  <div class="apps-container" id="appsContainer">
    <?php
      $q_args = [
        'post_type'      => 'app',
        'orderby'        => 'title',
        'order'          => 'ASC',
        'posts_per_page' => -1,
      ];
      $q = new WP_Query($q_args);
      if($q->have_posts()):
        while($q->have_posts()):
          $q->the_post();
          $icon_id  = get_post_meta(get_the_ID(), '_app_icon_id', true);
          $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'medium') : 'https://via.placeholder.com/64?text=No+Icon';
          $langs    = wp_get_post_terms(get_the_ID(), 'code_language');
          $lang_lbl = !empty($langs) ? $langs[0]->name : 'Unknown';
    ?>
      <md-outlined-card class="app-card" data-href="<?php the_permalink(); ?>">
        <img class="app-icon" src="<?php echo esc_url($icon_url); ?>" alt="App Icon">
        <h2 class="app-title"><?php the_title(); ?></h2>
        <div class="lang-tag"><?php echo esc_html($lang_lbl); ?></div>
      </md-outlined-card>
    <?php
        endwhile;
        wp_reset_postdata();
      else:
        echo '<p>No apps found.</p>';
      endif;
    ?>
  </div>

  <div class="bottom-spacing"></div>
</div>

<md-dialog id="langDialog" aria-label="Choose a language" style="--md-dialog-container-color:#fff;">
  <div slot="headline">Choose your language</div>
  <div slot="content">
    <form id="langForm" method="dialog">
      <div class="lang-radio-container">
        <?php
          $langs = get_terms([
            'taxonomy' => 'code_language',
            'hide_empty' => false
          ]);
          echo '<label class="lang-radio-label"><md-radio name="lang" value="0" checked></md-radio> <i class="fa fa-check-circle"></i>All</label>';
          foreach($langs as $lg) {
            echo '<label class="lang-radio-label">
                    <md-radio name="lang" value="'.esc_attr($lg->term_id).'"></md-radio>
                    <i class="fa fa-check-circle"></i>'
                 . esc_html($lg->name).
                 '</label>';
          }
        ?>
      </div>
    </form>
  </div>
  <div slot="actions">
    <md-text-button dialogaction="cancel" form="langForm">Cancel</md-text-button>
    <md-text-button dialogaction="ok" form="langForm" value="ok" autofocus>OK</md-text-button>
  </div>
</md-dialog>

<script>
(function($){
  $(function(){
    const ajaxLoader = document.getElementById('ajaxLoader');
    const container  = document.getElementById('appsContainer');
    const langDialog = document.getElementById('langDialog');
    const openBtn    = document.getElementById('openLangDialog');

    container.addEventListener('click', (event)=>{
      let card = event.target.closest('.app-card');
      if(card){
        let url = card.dataset.href;
        if(url){
          window.location.href = url;
        }
      }
    });

    const sortButton = document.getElementById('sortAZ');
    sortButton.addEventListener('click', ()=>{
      const cards = Array.from(container.querySelectorAll('.app-card'));
      cards.sort((a,b)=>{
        let tA = (a.querySelector('.app-title') || a.querySelector('h3')).textContent.trim().toLowerCase();
        let tB = (b.querySelector('.app-title') || b.querySelector('h3')).textContent.trim().toLowerCase();
        return tA.localeCompare(tB);
      });
      cards.forEach(card => container.appendChild(card));
    });

    openBtn.addEventListener('click', ()=>{
      langDialog.show();
    });

    langDialog.addEventListener('close', ()=>{
      if (langDialog.returnValue !== 'ok') return;

      const form   = document.getElementById('langForm');
      const formData = new FormData(form);
      const chosenLang = formData.get('lang') || '0';

      ajaxLoader.style.display = 'block';

      $.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        method: 'POST',
        data: {
          action: 'filter_apps',
          lang: chosenLang
        },
        beforeSend: function(){
          $('#appsContainer').css('opacity', '0.5');
        },
        success: function(response){
          console.log("Server says:", response);

          let parser = new DOMParser();
          let doc = parser.parseFromString(response, 'text/html');

          let cardDivs = doc.querySelectorAll('.app-card');
          cardDivs.forEach(div => {
            let newCard = doc.createElement('md-outlined-card');
            newCard.className = 'app-card';
            Array.from(div.attributes).forEach(attr => {
              newCard.setAttribute(attr.name, attr.value);
            });
            while(div.firstChild){
              newCard.appendChild(div.firstChild);
            }
            div.replaceWith(newCard);
          });

          let h3s = doc.querySelectorAll('.app-card h3');
          h3s.forEach(h3 => {
            let newH2 = doc.createElement('h2');
            newH2.className = 'app-title';
            newH2.innerHTML = h3.innerHTML;
            h3.replaceWith(newH2);
          });

          let newHTML = doc.body.innerHTML;
          $('#appsContainer').html(newHTML).css('opacity','1');

          window.reInitMaterialElements();
          ajaxLoader.style.display = 'none';
        },
        error: function(){
          alert('Error loading apps.');
          $('#appsContainer').css('opacity', '1');
          ajaxLoader.style.display = 'none';
        }
      });
    });
  });
})(jQuery);
</script>

<?php get_footer(); ?>