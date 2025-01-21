<?php
/**
 * Template Name: AppMage - Users List
 * Template Post Type: page
 *
 * Ce fichier affiche une liste de tous les utilisateurs (sauf les administrateurs) avec 
 * leur avatar, nom et nombre d'applications téléchargées. Il inclut également une barre 
 * de recherche pour filtrer les utilisateurs en temps réel. Les données des utilisateurs 
 * sont préparées en PHP et passées au JavaScript pour le rendu dynamique.
 */
get_header();
?>


<div class="users-wrapper">
  <div class="users-header">
    <h1>All Users</h1>
  </div>

  <div class="search-container">
    <md-outlined-text-field id="userSearch" placeholder="Search for users">
      <i class="fa fa-search" slot="leading-icon"></i>
    </md-outlined-text-field>
  </div>

  <div class="users-grid" id="usersGrid">

  </div>

  <p class="no-users" id="noUsersMsg" style="display:none;">No matching users found.</p>
</div>

<script>
  const userData = <?php
    $all_users = get_users([
      'role__not_in' => ['Administrator'],
      'fields' => 'all',
    ]);

    $results = [];
    foreach($all_users as $u) {
      $u_id = $u->ID;
      $apps_count = new WP_Query([
        'post_type' => 'app',
        'post_status' => 'publish',
        'author' => $u_id,
        'fields' => 'ids',
      ]);
      $count = $apps_count->found_posts;
      wp_reset_postdata();

      $display_name = $u->display_name ? $u->display_name : $u->user_login;
      $avatar_url   = get_avatar_url($u_id, ['size'=>64]) ?: 'https://via.placeholder.com/64?text=No+Avatar';

      $results[] = [
        'id' => $u_id,
        'name' => $display_name,
        'avatar' => $avatar_url,
        'count' => $count
      ];
    }
    echo json_encode($results);
  ?>;
</script>


<script type="module">
  import '@material/web/all.js';
import { styles as typescaleStyles } from '@material/web/typography/md-typescale-styles.js';
document.adoptedStyleSheets.push(typescaleStyles.styleSheet);

document.addEventListener('DOMContentLoaded', () => {
  const usersGrid = document.getElementById('usersGrid');
  const noUsersMsg = document.getElementById('noUsersMsg');
  const userSearch = document.getElementById('userSearch');

  function renderUsers(list) {
    usersGrid.innerHTML = '';
    if (list.length === 0) {
      noUsersMsg.style.display = 'block';
      return;
    } else {
      noUsersMsg.style.display = 'none';
    }

    list.forEach(u => {
      const div = document.createElement('div');
      div.className = 'user-card';
      div.innerHTML = `
        <img class="avatar" src="${u.avatar}" alt="Avatar">
        <div class="username">${u.name}</div>
        <div class="uploaded-count">Uploaded apps: ${u.count}</div>
      `;
      div.addEventListener('click', () => {
        window.location.href = `${window.location.origin}/user-profile?user=${u.id}`;
      });
      usersGrid.appendChild(div);
    });
  }

  renderUsers(userData);

  userSearch.addEventListener('input', () => {
    const query = userSearch.value.toLowerCase().trim();
    const filtered = userData.filter(u => u.name.toLowerCase().includes(query));
    renderUsers(filtered);
  });
});
</script>

<?php get_footer(); ?>
