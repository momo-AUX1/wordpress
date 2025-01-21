<?php
/**
 * Template Name: AppMage - User Profile
 * Template Post Type: page
 *
 * Ce fichier affiche le profil d'un utilisateur, incluant son avatar, son nom affiché,
 * et la liste de ses applications téléchargées. Si aucun utilisateur n'est trouvé, 
 * un message d'erreur est affiché. Les applications sont présentées sous forme de cartes 
 * Material Design avec leurs icônes, titres et langages associés.
 */
?>
<?php get_header(); ?>

<script type="module">
  import '@material/web/all.js';
  import { styles as typescaleStyles } from '@material/web/typography/md-typescale-styles.js';
  document.adoptedStyleSheets.push(typescaleStyles.styleSheet);
</script>

<?php
$user_id = 0;
if (isset($_GET['user'])) {
  if (is_numeric($_GET['user'])) {
    $user_id = intval($_GET['user']);
  } else {
    // Recherche de l'utilisateur par slug si l'ID n'est pas numérique
    $found_user = get_user_by('slug', sanitize_text_field($_GET['user']));
    if ($found_user) {
      $user_id = $found_user->ID;
    }
  }
}
if (!$user_id) {
  $user_id = get_current_user_id();
}
$user_info = get_userdata($user_id);
if (!$user_info) {
  echo '<div class="profile-container"><p style="color:#A00;">Utilisateur non trouvé.</p></div>';
  get_footer();
  exit;
}
$display_name = $user_info->display_name ?: $user_info->user_login;
$avatar_url   = get_avatar_url($user_id, ['size' => 128]) ?: 'https://via.placeholder.com/128?text=No+Avatar';

$args = [
  'post_type'      => 'app',
  'author'         => $user_id,
  'posts_per_page' => -1,
  'orderby'        => 'date',
  'order'          => 'DESC'
];
$apps_query = new WP_Query($args);
?>

<div class="profile-container">
  <img class="user-avatar" src="<?php echo esc_url($avatar_url); ?>" alt="Avatar de l'utilisateur">

  <div class="profile-username"><?php echo esc_html($display_name); ?></div>

  <?php if ($user_id === get_current_user_id()) : ?>
    <md-filled-button class="logout-button" onclick="window.location.href='<?php echo wp_logout_url(); ?>'">
      Se déconnecter
    </md-filled-button>
  <?php endif; ?>

  <h2 style="font-size:1.2rem;color:#880E4F;margin-bottom:1rem;">Applications téléchargées</h2>
  <?php if ($apps_query->have_posts()) : ?>
    <div class="apps-grid">
      <?php while ($apps_query->have_posts()) : $apps_query->the_post();
        $icon_id  = get_post_meta(get_the_ID(), '_app_icon_id', true);
        $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'medium') : 'https://via.placeholder.com/64?text=No+Icon';
        $langs    = wp_get_post_terms(get_the_ID(), 'code_language');
        $lang_lbl = (!empty($langs)) ? $langs[0]->name : 'Inconnu';
      ?>
      <md-outlined-card class="app-card" onclick="window.location.href='<?php the_permalink(); ?>'">
        <img class="app-icon" src="<?php echo esc_url($icon_url); ?>" alt="Icône de l'application">
        <h2 class="app-title"><?php the_title(); ?></h2>
        <div class="lang-tag"><?php echo esc_html($lang_lbl); ?></div>
      </md-outlined-card>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  <?php else : ?>
    <p class="no-apps">
      <?php echo ($user_id == get_current_user_id()) ? 'Vous n’avez encore téléchargé aucune application.' : 'Cet utilisateur n’a téléchargé aucune application.'; ?>
    </p>
  <?php endif; ?>
</div>

<?php get_footer(); ?>
