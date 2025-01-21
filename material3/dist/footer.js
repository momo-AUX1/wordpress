
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
