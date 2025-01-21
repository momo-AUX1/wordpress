<?php
/**
 * Fonctionnalités du thème AppMage
 *
 * Ce fichier gère l'enregistrement et l'enfilement des styles et scripts, 
 * l'enregistrement des menus de navigation, le support des fonctionnalités du thème, 
 * les requêtes AJAX pour filtrer les applications, l'approbation automatique des commentaires, 
 * et la création des pages de profil utilisateur et de liste des utilisateurs lors de l'activation ou du changement de thème.
 */

/**
 * Enfile les styles et scripts nécessaires au thème.
 *
 * Cette fonction enfile les polices Google, les icônes, Font Awesome, et les fichiers CSS et JS personnalisés 
 * pour différentes pages du thème AppMage.
 */
if ( ! function_exists( 'mmi_enqueues' ) ) {
    function mmi_enqueues() {
        $theme = wp_get_theme();
        $theme_version = $theme->get( 'Version' );
     
        wp_enqueue_style(
            'google-fonts',
            'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap',
            array(),
            null 
        );
      
        wp_enqueue_style(
            'google-icons',
            'https://fonts.googleapis.com/icon?family=Material+Icons',
            array(),
            null 
        );
      
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css',
            array(),
            null 
        );

        wp_enqueue_style(
            'main-css',
            get_template_directory_uri() . '/dist/style.css',
            array(),
            $theme_version
        );

        wp_enqueue_script(
            'main-js',
            get_template_directory_uri() . '/dist/index.js',
            array(),
            $theme_version,
            true 
        );
      
        wp_enqueue_style(
            'single-css',
            get_template_directory_uri() . '/dist/single-app.css',
            array(),
            $theme_version
        );

        wp_enqueue_script(
            'single-js',
            get_template_directory_uri() . '/dist/single-app.js',
            array(),
            $theme_version,
            true 
        );
      
        wp_enqueue_style(
            '404-css',
            get_template_directory_uri() . '/dist/404.css',
            array(),
            $theme_version
        );

        wp_enqueue_script(
            '404-js',
            get_template_directory_uri() . '/dist/404.js',
            array(),
            $theme_version,
            true 
        );
      
        wp_enqueue_style(
            'footer-css',
            get_template_directory_uri() . '/dist/footer.css',
            array(),
            $theme_version
        );
      
        wp_enqueue_style(
            'front-css',
            get_template_directory_uri() . '/dist/front-page.css',
            array(),
            $theme_version
        );
      
        wp_enqueue_style(
            'users-css',
            get_template_directory_uri() . '/dist/users-list.css',
            array(),
            $theme_version
        );

        wp_enqueue_script(
            'users-js',
            get_template_directory_uri() . '/dist/users-list.js',
            array(),
            $theme_version,
            true 
        );
      
        wp_enqueue_style(
            'profile-css',
            get_template_directory_uri() . '/dist/user-profile.css',
            array(),
            $theme_version
        );

    }

    /**
     * Ajoute l'attribut type="module" aux scripts spécifiés.
     *
     * Cette fonction ajoute l'attribut `type="module"` au script principal 'main-js' pour 
     * permettre l'utilisation des modules JavaScript.
     *
     * @param string $tag    Le tag HTML du script.
     * @param string $handle Le handle du script.
     * @param string $src    L'URL du script.
     * @return string Le tag script modifié si applicable, sinon le tag original.
     */
    function add_type_attribute( $tag, $handle, $src ) {
        if ( 'main-js' === $handle ) {
            return '<script type="module" src="' . esc_url( $src ) . '"></script>';
        }
        return $tag;
    }
    add_filter( 'script_loader_tag', 'add_type_attribute', 10, 3 );
}

add_action( 'wp_enqueue_scripts', 'mmi_enqueues' );

/**
 * Enregistre les emplacements de menus de navigation du thème.
 *
 * Cette fonction enregistre les emplacements pour le menu principal et le menu du pied de page 
 * afin de permettre leur gestion via l'interface d'administration de WordPress.
 */
function mmi_theme_register_menus() {
    register_nav_menus(array(
        'menu-principal' => __('Menu Principal', 'mmi-theme'),
        'menu-footer'    => __('Menu Pied de page', 'mmi-theme'),
    ));
}
add_action('init', 'mmi_theme_register_menus');

/**
 * Active les fonctionnalités du thème.
 *
 * Cette fonction active le support pour le logo personnalisé et l'icône du site, permettant 
 * ainsi aux utilisateurs de personnaliser ces éléments via l'interface d'administration de WordPress.
 */
function mmi_theme_support() {
    add_theme_support('custom-logo'); 
    add_theme_support('site-icon'); 
}
add_action('after_setup_theme', 'mmi_theme_support');

/**
 * Gère la requête AJAX pour filtrer les applications par langage de programmation.
 *
 * Cette fonction récupère les applications filtrées selon le langage spécifié, génère 
 * le HTML correspondant et le renvoie en réponse à la requête AJAX.
 */
add_action('wp_ajax_filter_apps', 'my_ajax_filter_apps');
add_action('wp_ajax_nopriv_filter_apps', 'my_ajax_filter_apps');
function my_ajax_filter_apps() {
    $lang_id = isset($_POST['lang']) ? intval($_POST['lang']) : 0;

    $args = [
        'post_type' => 'app',
        'orderby'   => 'title',
        'order'     => 'ASC',
        'posts_per_page' => -1,
    ];

    if($lang_id) {
        $args['tax_query'] = [[
            'taxonomy' => 'code_language',
            'field'    => 'term_id',
            'terms'    => $lang_id,
        ]];
    }

    $ajax_q = new WP_Query($args);
    if($ajax_q->have_posts()):
        ob_start();
        while($ajax_q->have_posts()):
            $ajax_q->the_post();
            $icon_id  = get_post_meta(get_the_ID(), '_app_icon_id', true);
            $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'medium') : 'https://via.placeholder.com/64?text=No+Icon';
            $languages = wp_get_post_terms(get_the_ID(), 'code_language');
            $lang_label = (!empty($languages)) ? $languages[0]->name : 'Unknown';
            ?>
            <div class="app-card" data-href="<?php the_permalink(); ?>">
              <img class="app-icon" src="<?php echo esc_url($icon_url); ?>" alt="App Icon">
              <h3><?php the_title(); ?></h3>
              <div class="lang-tag"><?php echo esc_html($lang_label); ?></div>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
        $html = ob_get_clean();
        echo $html;
    else:
        echo '<p>No apps found.</p>';
    endif;

    wp_die(); 
}

/**
 * Approuve automatiquement tous les commentaires.
 *
 * Cette fonction force l'approbation de tous les commentaires soumis, contournant le système de modération.
 *
 * @param int    $approved  Le statut actuel du commentaire.
 * @param array  $commentdata Les données du commentaire.
 * @return int Le nouveau statut du commentaire (1 pour approuvé).
 */
add_filter('pre_comment_approved', 'appmage_auto_approve_comments', 99, 2);
function appmage_auto_approve_comments($approved, $commentdata) {
    return 1;
}

/**
 * Crée la page de profil utilisateur lors de l'activation du thème.
 *
 * Cette fonction vérifie si la page 'user-profile' existe déjà. Si ce n'est pas le cas, elle la crée 
 * et lui assigne le template 'user-profile.php'.
 */
function create_appmage_user_profile_page() {
    $page_slug = 'user-profile';
    $page_check = get_page_by_path($page_slug);
    if (!$page_check) {
        // Insère la page
        $new_page_id = wp_insert_post(array(
            'post_title'   => 'User Profile',
            'post_name'    => $page_slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ));

        if ($new_page_id && !is_wp_error($new_page_id)) {
            update_post_meta($new_page_id, '_wp_page_template', 'user-profile.php');
        }
    }
}
register_activation_hook(__FILE__, 'create_appmage_user_profile_page');

/**
 * Crée la page de liste des utilisateurs lors de l'activation du thème.
 *
 * Cette fonction vérifie si la page 'users-list' existe déjà. Si ce n'est pas le cas, elle la crée 
 * et lui assigne le template 'user-list.php'.
 */
function create_appmage_users_page() {
    $page_slug = 'users-list';
    $page_check = get_page_by_path($page_slug);
    if (!$page_check) {
        $new_page_id = wp_insert_post([
            'post_title'   => 'Users List',
            'post_name'    => $page_slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);
        if ($new_page_id && !is_wp_error($new_page_id)) {
            update_post_meta($new_page_id, '_wp_page_template', 'user-list.php');
        }
    }
}
register_activation_hook(__FILE__, 'create_appmage_users_page');

/**
 * Crée les pages de profil utilisateur et de liste des utilisateurs lors du changement de thème.
 *
 * Cette fonction est appelée lorsque le thème est changé, garantissant que les pages nécessaires 
 * sont présentes et correctement configurées avec les templates appropriés.
 */
add_action('after_switch_theme', 'create_appmage_user_profile_page');
add_action('after_switch_theme', 'create_appmage_users_page');

?>
