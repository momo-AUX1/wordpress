
<?php
/**
 * Footer Template
 *
 * Ce fichier gère le pied de page du site, incluant les onglets de navigation inférieurs, 
 * le bouton d'action flottant pour créer une nouvelle application, et les scripts JavaScript 
 * nécessaires pour la gestion de l'interaction utilisateur.
 */
?>


<script type="module">
  import '@material/web/all.js';
import { styles as typescaleStyles } from '@material/web/typography/md-typescale-styles.js';
document.adoptedStyleSheets.push(typescaleStyles.styleSheet);
window.reInitMaterialElements = () => {
  console.log('reInitMaterialElements()');
};
</script>

<?php
  $is_logged_in  = is_user_logged_in();
  $profile_label = $is_logged_in ? 'Profile' : 'Login';

  $current_user = wp_get_current_user();
  $current_roles = $current_user ? $current_user->roles : [];
?>

<div class="spacer"></div>

<md-tabs id="bottomTabs" aria-label="Bottom navigation">
  <md-primary-tab id="tabApps">
    <md-icon slot="icon">
      <i class="fas fa-th-large"></i>
    </md-icon>
    Apps
  </md-primary-tab>

  <md-primary-tab id="tabUsers">
    <md-icon slot="icon">
      <i class="fas fa-users"></i>
    </md-icon>
    Users
  </md-primary-tab>

  <md-primary-tab id="tabProfile">
    <md-icon slot="icon">
      <?php if (!$is_logged_in): ?>
        <i class="fas fa-sign-in-alt"></i>
      <?php else: ?>
        <i class="fas fa-user"></i>
      <?php endif; ?>
    </md-icon>
    <?php echo $profile_label; ?>
  </md-primary-tab>
</md-tabs>

<md-fab
  id="createAppFab"
  variant="primary"
  size="medium"
  label="New App"
  aria-label="Create new app"
>
  <md-icon slot="icon">
    <i class="fas fa-plus"></i>
  </md-icon>
</md-fab>


<script>
  
document.addEventListener('DOMContentLoaded', () => {
  const path = window.location.href; 
  const tabApps    = document.getElementById('tabApps');
  const tabUsers   = document.getElementById('tabUsers');
  const tabProfile = document.getElementById('tabProfile');
  
  const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
  const userRoles  = <?php echo json_encode($current_roles); ?>; 

  [tabApps, tabUsers, tabProfile].forEach(tab => tab.removeAttribute('active'));

  if (path.includes('/apps')) {
    tabApps.setAttribute('active', '');
  } else if (path.includes('/users-list')) {
    tabUsers.setAttribute('active', '');
  } else if (path.includes('/user-profile') || path.includes('/login')) {
    tabProfile.setAttribute('active', '');
  }

  const bottomTabs = document.getElementById('bottomTabs');
  bottomTabs.addEventListener('change', () => {
    const newIndex = bottomTabs.activeTabIndex;
    if (newIndex === 0) {
      window.location.href = '<?php echo home_url('/apps'); ?>';
    } else if (newIndex === 1) {
      window.location.href = '<?php echo home_url('/users-list'); ?>';
    } else if (newIndex === 2) {
      if (!isLoggedIn) {
        window.location.href = '<?php echo wp_login_url(); ?>';
      } else {
        window.location.href = '<?php echo home_url('/user-profile'); ?>';
      }
    }
  });

  const createAppFab = document.getElementById('createAppFab');
  if (
    isLoggedIn &&
    !userRoles.includes('fan') &&
    path === 'https://fi.nanodata.cloud/apps/'
  ) {
    createAppFab.style.display = 'inline-flex';
  }

  createAppFab.addEventListener('click', () => {
    window.location.href = 'https://fi.nanodata.cloud/wp-admin/post-new.php?post_type=app';
  });
});
</script>

<?php wp_footer(); ?>
</body>
</html>
