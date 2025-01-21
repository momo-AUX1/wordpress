<section id="comments">
	<?php if ( have_comments() ) : // S'il y a des commentaires sur le post ?>
		<h2 class="comments-title">
			<?php
				$comments_number = get_comments_number();
				if ( $comments_number === 1 ) {
					echo '1 commentaire'; 
				} else {
					echo $comments_number . ' commentaires';
				}
			?>
		</h2>
		<div class="comments-list">
			<?php 
				wp_list_comments(array(
					// Consultez la documentation ci-dessous pour personnaliser la fonction (facultatif)
				));
			?>
		</div>
	<?php endif; ?>
	
	<?php if ( !comments_open() ) {
		echo '<p>Les commentaires sont fermés.</p>';
	} else {
		// Affiche le formulaire de commentaires
		comment_form(array(
			// Consultez la document ci-dessous pour personnaliser la fonction (facultatif)
		));
	} ?>
</section>