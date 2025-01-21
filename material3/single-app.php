<?php
/*
 * Template Name: Single App (Material Comments)
 * Template Post Type: app
 */

/**
 * Template de la page unique pour les applications Appmage
 *
 * Ce fichier affiche les détails d'une application individuelle, incluant son icône, son titre, 
 * le langage de programmation utilisé, et un lien de téléchargement. Il présente également 
 * les informations de l'auteur, le contenu de l'application, et une section de commentaires 
 * stylisée avec Material Design. Les interactions utilisateur, telles que la navigation et 
 * la soumission de commentaires, sont gérées via des scripts JavaScript intégrés.
 */
?>

<?php get_header(); ?>

<script type="module">
  import '@material/web/all.js';
  import {styles as typescaleStyles} from '@material/web/typography/md-typescale-styles.js';
  document.adoptedStyleSheets.push(typescaleStyles.styleSheet);
</script>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<div class="top-header">
  <button class="back-button" onclick="window.history.back()" aria-label="Go back">
    <span class="material-icons">arrow_back</span>
  </button>
  <h1><?php the_title(); ?></h1>
</div>

<div class="app-container">
  <div class="app-info">
    <?php
      $icon_id  = get_post_meta(get_the_ID(), '_app_icon_id', true);
      $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'medium') : 'https://via.placeholder.com/100?text=No+Icon';
    ?>
    <img class="app-icon" src="<?php echo esc_url($icon_url); ?>" alt="App Icon">

    <div class="app-info-text">
      <h2><?php the_title(); ?></h2>
      <?php
        $langs      = wp_get_post_terms(get_the_ID(), 'code_language');
        $lang_label = !empty($langs) ? $langs[0]->name : 'Unknown';
      ?>
      <p class="app-lang"><?php echo esc_html($lang_label); ?></p>

      <?php
        $github_link = get_post_meta(get_the_ID(), '_github_link', true);
        if(!empty($github_link)):
      ?>
        <md-filled-button
          class="app-download-btn"
          onclick="window.open('<?php echo esc_url($github_link); ?>','_blank')"
        >
          Download
        </md-filled-button>
      <?php else: ?>
        <md-filled-button class="app-download-btn" disabled>Download</md-filled-button>
      <?php endif; ?>
    </div>
  </div>

  <?php
    $author_id   = get_post_field('post_author', get_the_ID());
    $author_data = get_userdata($author_id);
    if ($author_data) {
      $author_name = $author_data->display_name ?: $author_data->user_login;
    } else {
      $author_name = 'Unknown User';
    }
  ?>
  <div class="published-info">
    Posted on <?php echo get_the_date(); ?> by
    <a class="author-link" href="<?php echo esc_url(home_url('/user-profile?user=' . $author_id)); ?>">
      <?php echo esc_html($author_name); ?>
    </a>
  </div>

  <div class="app-content">
    <?php the_content(); ?>
  </div>

  <section id="comments">
    <?php
      $all_comments = get_comments([
        'post_id' => get_the_ID(),
        'status'  => 'approve',
        'orderby' => 'comment_date_gmt',
        'order'   => 'ASC',
        'number'  => 9999
      ]);

      if($all_comments):
        $count = count($all_comments);
    ?>
      <h2 class="comments-title">
        <?php echo ($count === 1) ? '1 Message' : $count . ' Messages'; ?>
      </h2>
      <ol class="comment-list">
        <?php
          $app_owner_id = get_post_field('post_author', get_the_ID());
          wp_list_comments([
            'style'       => 'ol',
            'avatar_size' => 40,
            'type'        => 'comment',
            'callback'    => function($comment, $args, $depth) use($app_owner_id){
              $tag      = ($args['style'] === 'div') ? 'div' : 'li';
              $is_reply = (bool)$comment->comment_parent;
              $is_owner = ($comment->user_id == $app_owner_id);
              ?>
              <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class($is_reply ? 'comment reply':'comment'); ?>>
                <?php 
                  $bubbleClass = 'bubble';
                  if($is_reply) $bubbleClass .= ' reply';
                  if($is_owner) $bubbleClass .= ' owner-bubble';
                ?>
                <div class="<?php echo $bubbleClass; ?>">
                  <?php if($is_owner): ?>
                    <span class="owner-label">Owner</span>
                  <?php endif; ?>
                  <span class="comment-author">
                    <?php echo get_comment_author($comment->comment_ID); ?>
                  </span>
                  <div class="comment-text">
                    <?php comment_text(); ?>
                  </div>
                  <div class="comment-date-reply">
                    <span>
                      <?php echo get_comment_date('', $comment->comment_ID); ?>
                    </span>
                    <span class="comment-reply-link">
                      <?php 
                        comment_reply_link(array_merge($args, [
                          'depth' => $depth,
                          'max_depth' => $args['max_depth'],
                          'reply_text' => 'Reply'
                        ]));
                      ?>
                    </span>
                  </div>
                </div>
              </<?php echo $tag; ?>>
              <?php
            }
          ], $all_comments);
        ?>
      </ol>
    <?php else: ?>
      <p class="italic-message">No messages yet.</p>
    <?php endif; ?>

    <?php if(is_user_logged_in()): ?>
      <div class="comment-respond">
        <?php
        comment_form([
          'title_reply'          => 'Leave a Message',
          'title_reply_to'       => 'Reply to %s',
          'comment_notes_before' => '',
          'comment_notes_after'  => '',
          'fields'               => [],
          'label_submit'         => 'Send',
          'class_form'           => 'comment-form',
          'comment_field'        => '
            <md-filled-text-field
              id="mdComment"
              type="textarea"
              label="Your message"
              rows="3">
            </md-filled-text-field>
            <textarea id="comment" name="comment" required></textarea>
          ',
          'submit_button'        => '
            <md-filled-button id="mdSubmit" type="submit" class="submit-button">
              Send
            </md-filled-button>
          '
        ]);
        ?>
      </div>
    <?php else: ?>
      <p class="italic-message-login">
        You must be logged in to send a message.
      </p>
    <?php endif; ?>
  </section>
</div>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
