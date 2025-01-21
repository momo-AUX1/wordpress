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
