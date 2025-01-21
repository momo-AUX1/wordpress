      (function(){
        const mdField = document.getElementById('mdComment');
        const hiddenTA= document.getElementById('comment');
        const form = document.querySelector('form.comment-form');

        hiddenTA.style.display = 'none';

        mdField.addEventListener('input', ()=>{
          hiddenTA.value = mdField.value;
        });

        form.addEventListener('submit', ()=>{
          hiddenTA.value = mdField.value;
        });
      })();