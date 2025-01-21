# AppMage

AppMage est une application de gestion logicielle inspirée par le Google Play Store, utilisant le design Material Design 3 (Material3) pour offrir une expérience utilisateur moderne et intuitive.

## Caractéristiques

- **Gestion complète des applications** : Ajoutez, éditez, supprimez et organisez vos applications facilement.
- **Inspiration Google Play Store** : Interface familière et conviviale pour une navigation simplifiée.
- **Design Material3** : Esthétique moderne et composants réactifs basés sur les directives Material Design de Google.
- **Rôles et capacités personnalisés** : Gestion des utilisateurs avec des rôles spécifiques tels que "Fan" et "App Maker".
- **Workflow d'approbation** : Contrôlez la publication des applications grâce à un système d'approbation intégré.
- **Support des langages de programmation** : Filtrez et gérez les applications par langage de code grâce à une taxonomie dédiée.

## Installation

### Prérequis

1. **Node.js et npm** : Assurez-vous d'avoir Node.js et npm installés sur votre système.
2. **RPM** : Utilisé pour installer les dépendances nécessaires.

### Étapes d'installation

1. **Cloner le dépôt** :
    ```bash
    git clone https://github.com/momo-AUX1/wordpress.git
    cd wordpress
    ```

2. **Installer les dépendances Node.js** :
    Accédez au dossier du thème Material3 situé dans `/dist` et installez les modules nécessaires :
    ```bash
    cd wp-content/plugins/AppMage/theme/material3/dist/
    npm install
    ```

    Si vous utilisez `rpm` pour gérer vos paquets, vous pouvez également installer les dépendances via `rpm` si nécessaire.

3. **Activer l'extension et le thème** :
    - **Activer l'extension** :
        Rendez-vous dans le tableau de bord WordPress, puis allez dans **Extensions** et activez **AppMage**.
    - **Activer le thème** :
        Allez dans **Apparence > Thèmes** et activez le thème **AppMage**.

    **Remarque** : Dans certains cas, vous pourriez avoir besoin d'activer le thème deux fois pour que toutes les fonctionnalités soient correctement configurées.

## Configuration

Certaines pages HTML incluent leur propre JavaScript pour éviter les conflits avec le chargeur ESM de Material3. Cela garantit que les composants Material3 fonctionnent correctement sans erreurs de chargement.

### Étapes supplémentaires

1. **Vérifiez les dépendances** :
    Assurez-vous que tous les modules Node.js nécessaires sont installés dans le dossier `/material3/dist/node_modules`.
    
2. **Configurer les pages HTML** :
    Certaines pages HTML doivent inclure leurs propres scripts JavaScript pour assurer une compatibilité optimale avec Material3.

## Utilisation

Après l'installation et la configuration, vous pouvez commencer à utiliser AppMage pour gérer vos applications. L'interface inspirée par le Google Play Store permet une navigation intuitive et une gestion efficace des applications.

### Gestion des utilisateurs

- **Rôles** :
    - **Fan** : Peut uniquement lire les applications publiées.
    - **App Maker** : Peut créer et éditer ses propres applications sans pouvoir les publier.
    - **Administrateur** : Accès complet à toutes les fonctionnalités et capacités.

### Ajout et gestion des applications

1. **Ajouter une nouvelle application** :
    Allez dans **Apps > Ajouter** et remplissez les informations nécessaires telles que le nom, la description, l'icône, le langage de programmation, etc.
2. **Éditer une application** :
    Depuis la liste des applications, sélectionnez celle que vous souhaitez modifier et apportez les changements nécessaires.
3. **Supprimer une application** :
    Supprimez les applications non désirées directement depuis la liste des applications.

## Notes

- **Développement et personnalisation** :
    Le dossier `material3/dist/` contient les modules Node.js nécessaires pour le développement et la personnalisation du thème. Assurez-vous de maintenir ces dépendances à jour pour éviter tout problème de compatibilité.
  
