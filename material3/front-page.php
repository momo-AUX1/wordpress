<?php
/**
 * Template de la page d'accueil Appmage
 *
 * Ce fichier affiche la section héro avec l'icône du site et le titre, 
 * une section présentant les applications récentes, et un bouton d'appel à l'action 
 * pour naviguer vers la liste complète des applications. Il inclut également 
 * des scripts JavaScript pour gérer les interactions utilisateur.
 */
get_header(); ?>

<main>
  <div class="appmage-hero">
    <?php $site_icon_url = get_site_icon_url(64); ?>
    <?php if ($site_icon_url) : ?>
      <img src="<?php echo esc_url($site_icon_url); ?>" alt="Site Icon" />
    <?php endif; ?>
    <h1 class="app-title">Appmage</h1>
  </div>

  <section class="appmage-container">
    <p class="app-description">
      Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
    </p>

    <h2 class="section-title">Recent Apps</h2>
    <div class="appmage-recent-apps">
      <?php
      $apps_query = new WP_Query([
        'post_type' => 'app',
        'posts_per_page' => 3,
      ]);

      if ($apps_query->have_posts()) :
        while ($apps_query->have_posts()) : $apps_query->the_post();
          $github_link = get_post_meta(get_the_ID(), '_github_link', true);
          $icon_id = get_post_meta(get_the_ID(), '_app_icon_id', true);
          $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'medium') : '';
          $languages = wp_get_post_terms(get_the_ID(), 'code_language');
          $lang_name = !empty($languages) ? $languages[0]->name : 'Unknown';
      ?>
      <div class="app-card" data-href="<?php the_permalink(); ?>">
        <div class="shapes">
          <?php if ($icon_url) : ?>
            <img class="app-icon" src="<?php echo esc_url($icon_url); ?>" alt="App Icon">
          <?php else : ?>
            <img class="app-icon" src="https://via.placeholder.com/64?text=No+Icon" alt="No Icon">
          <?php endif; ?>
        </div>
        <h3 class="app-name"><?php the_title(); ?></h3>
        <div class="language-label"><?php echo esc_html($lang_name); ?></div>
      </div>
      <?php
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <p>No recent apps found.</p>
      <?php endif; ?>
    </div>

    <div class="cta-button">
      <md-filled-button class="go-button" data-link="<?php echo esc_url(home_url('/apps')); ?>">Go</md-filled-button>
    </div>
  </section>
</main>

<script type="module">
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
</script>

<?php get_footer(); ?>
