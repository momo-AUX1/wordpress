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