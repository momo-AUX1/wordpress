<?php
/*
Plugin Name:       AppMage
Plugin URI:        https://example.com/
Description:       Complete app management with custom roles, capabilities, and an approval workflow. Includes code language dropdown.
Version:           1.0
Requires at least: 5.2
Requires PHP:      7.2
Author:            Mohammed alshanqiti
Author URI:        https://github.com/momo-AUX1
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:       appmage
Domain Path:       /languages
*/

/**
 * Enregistre un log lors de la sauvegarde d'un post.
 */
function enregistrer_log_sauvegarde($post_id) {
    $log_message = "Le post avec l'ID $post_id a été sauvegardé.\n";
    file_put_contents(__DIR__ . '/save_log.txt', $log_message, FILE_APPEND);
}
add_action('save_post', 'enregistrer_log_sauvegarde');

/**
 * Active le plugin (rôles, capacités, etc.) et vide les permaliens.
 */
function _on_activate() {
    setup_appmage_roles_and_caps();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, '_on_activate');

/**
 * Actions lors de la désactivation du plugin.
 */
function _on_deactivate() {
    // Aucune action spécifiée ici
}
register_deactivation_hook(__FILE__, '_on_deactivate');

/**
 * Actions lors de la désinstallation du plugin.
 */
function _on_delete() {
    // Aucune action spécifiée ici
}
register_uninstall_hook(__FILE__, '_on_delete');

/**
 * Enregistre le type de contenu personnalisé 'app'.
 */
function appmage_register_post_type() {
    $labels = [
        'name'               => __('Apps', 'appmage'),
        'singular_name'      => __('App', 'appmage'),
        'add_new'            => __('Add New App', 'appmage'),
        'add_new_item'       => __('Add New App', 'appmage'),
        'edit_item'          => __('Edit App', 'appmage'),
        'new_item'           => __('New App', 'appmage'),
        'view_item'          => __('View App', 'appmage'),
        'search_items'       => __('Search Apps', 'appmage'),
        'not_found'          => __('No apps found', 'appmage'),
        'not_found_in_trash' => __('No apps found in Trash', 'appmage'),
        'all_items'          => __('All Apps', 'appmage'),
        'menu_name'          => __('Apps', 'appmage'),
    ];

    $capabilities = [
        'edit_post'             => 'edit_app',
        'read_post'             => 'read_app',
        'delete_post'           => 'delete_app',
        'edit_posts'            => 'edit_apps',
        'edit_others_posts'     => 'edit_others_apps',
        'publish_posts'         => 'publish_apps',
        'read_private_posts'    => 'read_private_apps',
        'delete_posts'          => 'delete_apps',
        'delete_private_posts'  => 'delete_private_apps',
        'delete_published_posts'=> 'delete_published_apps',
        'delete_others_posts'   => 'delete_others_apps',
        'edit_private_posts'    => 'edit_private_apps',
        'edit_published_posts'  => 'edit_published_apps',
        'create_posts'          => 'edit_apps',
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'supports'           => ['title', 'editor', 'author', 'thumbnail', 'custom-fields', 'comments'],
        'capability_type'    => ['app', 'apps'],
        'capabilities'       => $capabilities,
        'map_meta_cap'       => true,
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'apps'],
        'show_in_rest'       => true,
    ];

    register_post_type('app', $args);
}
add_action('init', 'appmage_register_post_type');

/**
 * Enregistre la taxonomie 'code_language'.
 */
function appmage_register_taxonomies() {
    $labels = [
        'name'          => __('Code Languages', 'appmage'),
        'singular_name' => __('Code Language', 'appmage'),
        'search_items'  => __('Search Code Languages', 'appmage'),
        'all_items'     => __('All Code Languages', 'appmage'),
        'edit_item'     => __('Edit Code Language', 'appmage'),
        'update_item'   => __('Update Code Language', 'appmage'),
        'add_new_item'  => __('Add New Code Language', 'appmage'),
        'menu_name'     => __('Code Languages', 'appmage'),
    ];

    $args = [
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'code-language'],
        'public'            => true,
        'capabilities'      => [
            'manage_terms' => 'manage_options',
            'edit_terms'   => 'manage_options',
            'delete_terms' => 'manage_options',
            'assign_terms' => 'edit_apps',
        ],
    ];

    register_taxonomy('code_language', 'app', $args);

    $default_languages = ['C++', 'Java', 'Python'];
    foreach ($default_languages as $lang) {
        if (!term_exists($lang, 'code_language')) {
            wp_insert_term($lang, 'code_language');
        }
    }
}
add_action('init', 'appmage_register_taxonomies');

/**
 * Ajoute la metabox "App Details" pour le type 'app'.
 */
function appmage_add_app_meta_boxes() {
    add_meta_box('app_details_metabox', 'App Details', 'appmage_app_details_metabox', 'app', 'normal', 'high');
}
add_action('add_meta_boxes', 'appmage_add_app_meta_boxes');

/**
 * Charge les scripts pour la page d'édition d'une application.
 */
function appmage_admin_scripts($hook) {
    global $post;
    if (($hook == 'post.php' || $hook == 'post-new.php') && isset($post) && $post->post_type == 'app') {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'appmage_admin_scripts');

/**
 * Gère le contenu de la metabox "App Details".
 */
function appmage_app_details_metabox($post) {
    $github_link = get_post_meta($post->ID, '_github_link', true);
    $icon_id = get_post_meta($post->ID, '_app_icon_id', true);
    $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'thumbnail') : '';

    $current_terms = wp_get_post_terms($post->ID, 'code_language', ['fields' => 'ids']);
    $current_term_id = (!empty($current_terms)) ? $current_terms[0] : '';

    $languages = get_terms(['taxonomy' => 'code_language', 'hide_empty' => false]);
    $can_assign = current_user_can('edit_apps');
    ?>
    <p>
        <label for="github_link"><?php _e('GitHub URL:', 'appmage'); ?></label><br>
        <input type="url" name="github_link" id="github_link" value="<?php echo esc_attr($github_link); ?>" style="width:100%;" placeholder="https://github.com/your-project">
    </p>
    <p>
        <label for="app_language"><?php _e('Code Language (required):', 'appmage'); ?></label><br>
        <select name="app_language" id="app_language" <?php disabled(!$can_assign); ?>>
            <option value=""><?php _e('Select a language', 'appmage'); ?></option>
            <?php foreach ($languages as $lang): ?>
                <option value="<?php echo esc_attr($lang->term_id); ?>" <?php selected($lang->term_id, $current_term_id); ?>>
                    <?php echo esc_html($lang->name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!$can_assign): ?>
            <br><em><?php _e('You cannot change the language.', 'appmage'); ?></em>
        <?php endif; ?>
    </p>
    <p>
        <label><?php _e('App Icon:', 'appmage'); ?></label><br>
        <input type="hidden" name="app_icon_id" id="app_icon_id" value="<?php echo esc_attr($icon_id); ?>">
        <button type="button" class="button upload-app-icon" <?php disabled(!$can_assign); ?>>
            <?php _e('Choose Icon', 'appmage'); ?>
        </button>
        <button type="button" class="button remove-app-icon" style="margin-left:10px;" <?php disabled(!$can_assign); ?>>
            <?php _e('Remove', 'appmage'); ?>
        </button>
        <div class="app-icon-preview" style="margin-top:10px;">
            <?php if ($icon_url): ?>
                <img src="<?php echo esc_url($icon_url); ?>" style="max-width:100px;height:auto;"/>
            <?php endif; ?>
        </div>
    </p>
    <script>
    jQuery(document).ready(function($){
        var frame;
        $('.upload-app-icon').on('click', function(e){
            e.preventDefault();
            if ($(this).prop('disabled')) return;
            if (frame) {
                frame.open();
                return;
            }
            frame = wp.media({
                title: '<?php _e("Select or Upload Icon", "appmage"); ?>',
                button: { text: '<?php _e("Use this icon", "appmage"); ?>' },
                multiple: false
            });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#app_icon_id').val(attachment.id);
                $('.app-icon-preview').html('<img src="'+attachment.url+'" style="max-width:100px;height:auto;"/>');
            });
            frame.open();
        });

        $('.remove-app-icon').on('click', function(e){
            e.preventDefault();
            if ($(this).prop('disabled')) return;
            $('#app_icon_id').val('');
            $('.app-icon-preview').html('');
        });
    });
    </script>
    <?php
}

/**
 * Sauvegarde les données de la metabox "App Details".
 */
function appmage_save_app_meta_boxes($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['github_link'])) {
        update_post_meta($post_id, '_github_link', esc_url_raw($_POST['github_link']));
    }

    if (isset($_POST['app_icon_id'])) {
        $icon_id = intval($_POST['app_icon_id']);
        update_post_meta($post_id, '_app_icon_id', $icon_id);
    }

    if (isset($_POST['app_language'])) {
        $language_id = intval($_POST['app_language']);
        if ($language_id) {
            wp_set_object_terms($post_id, $language_id, 'code_language', false);
        } else {
            wp_set_object_terms($post_id, [], 'code_language', false);
        }
    }
}
add_action('save_post_app', 'appmage_save_app_meta_boxes');

/**
 * Crée et ajuste les rôles/capacités pour AppMage.
 */
function setup_appmage_roles_and_caps() {
    remove_role('app_maker');
    remove_role('fan');

    add_role('fan', __('Fan', 'appmage'), [
        'read' => true,
        'read_app' => true,
        'read_apps' => true,
    ]);

    add_role('app_maker', __('App Maker', 'appmage'), [
        'read' => true,
        'upload_files' => true,
        'edit_app' => true,
        'edit_apps' => true,
        'delete_app' => true,
        'delete_apps' => true,
        'edit_published_apps' => false,
        'publish_apps' => false,
        'edit_others_apps' => false,
        'delete_others_apps' => false,
    ]);

    $admin = get_role('administrator');
    if ($admin) {
        $caps = [
            'edit_app', 'read_app', 'delete_app',
            'edit_apps', 'edit_others_apps', 'publish_apps', 'read_private_apps',
            'delete_apps', 'delete_others_apps', 'edit_published_apps', 'delete_published_apps',
            'edit_private_apps', 'delete_private_apps'
        ];
        foreach ($caps as $cap) {
            $admin->add_cap($cap);
        }
    }
}

/**
 * Empêche la publication d'une app sans langage défini.
 */
add_filter('wp_insert_post_data', 'appmage_enforce_code_language', 10, 2);
function appmage_enforce_code_language($data, $postarr) {
    if (isset($postarr['post_type']) && 'app' === $postarr['post_type']) {
        if ('publish' === $data['post_status'] || 'future' === $data['post_status']) {
            $langs = wp_get_post_terms($postarr['ID'], 'code_language', ['fields' => 'ids']);
            if (empty($langs)) {
                $data['post_status'] = 'draft';
                add_filter('redirect_post_location', function($location) {
                    return add_query_arg('message', '1', $location);
                }, 99);
                add_action('admin_notices', function() {
                    echo '<div class="error"><p>You must select a Code Language before publishing.</p></div>';
                });
            }
        }
    }
    return $data;
}

/**
 * Exemples d'options et de page d'options (facultatif).
 */
add_option("sae_custom_option","test__");
add_option("app_name","nom");
add_option("app_desc","desc");

/**
 * Supprime les options personnalisées à la désinstallation.
 */
function sae_uninstall_plugin_function() {
    delete_option('sae_custom_option');
    delete_option("app_name");
    delete_option("app_desc");
}
register_uninstall_hook(__FILE__, 'sae_uninstall_plugin_function');

/**
 * Enregistre la page de réglages APPMAGE dans le menu d'administration.
 */
function sae_register_options_page() {
    add_menu_page(
        'Réglages APPMAGE',
        'Réglages APPMAGE',
        'manage_options',
        'sae_settings',
        'sae_options_page_html'
    );
}
add_action('admin_menu', 'sae_register_options_page');

/**
 * Initialise les réglages et sections de la page d'options.
 */
function sae_settings_init() {
    register_setting('sae_settings_group', 'sae_custom_option');
    register_setting('sae_settings_group', 'app_name');
    register_setting('sae_settings_group', 'app_desc');

    add_settings_section(
        'sae_section',
        'Section APPMAGE',
        'sae_section_callback',
        'sae_settings'
    );

    add_settings_field(
        'sae_field',
        'Champ texte',
        'sae_field_callback',
        'sae_settings',
        'sae_section'
    );
}
add_action('admin_init', 'sae_settings_init');

/**
 * Affiche un texte d'introduction dans la section de réglages.
 */
function sae_section_callback() {
    echo '<p>Paramètres personnalisés pour le plugin APPMAGE.</p>';
}

/**
 * Affiche les champs de saisie dans la page d'options.
 */
function sae_field_callback() {
    $value  = get_option('sae_custom_option');
    $value2 = get_option('app_name');
    $value3 = get_option('app_desc');
    ?>
    <label for="sae_custom_option">Option personnalisée :</label>
    <input type="text" name="sae_custom_option" id="sae_custom_option" value="<?php echo esc_attr($value); ?>">
    <br><br>
    <label for="app_name">Nom de l'application :</label>
    <input type="text" name="app_name" value="<?php echo esc_attr($value2); ?>">
    <br><br>
    <label for="app_desc">Description de l'application :</label>
    <input type="text" name="app_desc" value="<?php echo esc_attr($value3); ?>">
    <?php
}

/**
 * Affiche la page HTML de réglages APPMAGE.
 */
function sae_options_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_GET['settings-updated'])) {
        add_settings_error('sae_messages', 'sae_message', 'Réglages sauvegardés', 'updated');
    }

    settings_errors('sae_messages');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('sae_settings_group');
            do_settings_sections('sae_settings');
            submit_button('Enregistrer les réglages');
            ?>
        </form>
    </div>
    <?php
}
