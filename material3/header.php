<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <?php
    /**
     * Template d'en-tête du site
     *
     * Ce fichier définit la structure de l'en-tête HTML, incluant les métadonnées, 
     * l'importation des composants Material Web via un importmap, et prépare 
     * l'intégration du menu de navigation principal (actuellement commenté).
     */
    ?>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <header>
       <!-- Menu de navigation principal (actuellement désactivé)
       <?php
          wp_nav_menu(array(
            'theme_location'  => 'menu-principal',
            'container'       => 'nav',
            'container_class' => 'menu-container',
            'menu_class'      => 'menu-list',
       ));
       ?> -->
      <script type="importmap">
    {
      "imports": {
        "@material/web/": "https://esm.run/@material/web/"
      }
    }
    </script>
    </header>
