# PROAXIVE (1.6.x)
### version 1.6.0-beta7

[![Minimum PHP Version](https://img.shields.io/badge/PHP->=7.4-%23786fa6)](https://php.net/)
[![Minimum MySQL Version](https://img.shields.io/badge/MySQL-5.x-%23f0932b)](https://www.mysql.com/fr/)
[![Require Composer](https://img.shields.io/badge/Composer-2.0.8-green)](https://www.mysql.com/fr/)

Proaxive est une application web dédiée aux techniciens informatique
Elle permet de gérer les interventions informatique en ligne. L'application web Proaxive a pour but de simplifier le suivi en atelier. Vos clients peuvent suivre ce qu'il se passe sur leur PC en temps réel.

![ScreenShot](https://proaxive.fr/uploads/img/Proaxive_1-6-0-1.jpg)

#### Requis
- PHP >7.4
- MySQL 5.x
- Apache or Nginx
- Composer (SSH)
- Git (pas obligatoire)

#### Extensions PHP requises
- php-intl
- php-xml

### Licence

Proaxive est distribué sous les termes de la licence GNU General Public License v3+ ou supérieure.

**Les adresses postales sont récupérées via une API.**

API Adresse : https://adresse.data.gouv.fr/api-doc/adresse

**La carte utilisée dans les fiches clientes** :

OpenStreetMap : https://www.openstreetmap.fr/

## Installation
    
```shell
composer create-project -s dev selmak-fr/proaxive .
```
01. Via votre terminal, éditez le fichier phinx.yml (à la racine de l'application) de Proaxive et renseignez les informations de votre base de données (section "development").

*Avant de lancer la migration, créez la base de données qui sera utilisée par Proaxive*   

Pour créer les tables lancez la commande

```shell
vendor/bin/phinx migrate
```

Afin d'ajouter le compte administrateur et les données par défaut, lancez le seeding

```shell
vendor/bin/phinx seed:run
```

Par défaut, Proaxive utilise un fichier .htaccess afin de rediriger vers *public*   
Vous pouvez très bien créer un virtualhost à la place (n'oubliez pas de renommer ou supprimer le .htaccess de la racine)

- Accédez au nom de domaine de votre installation Proaxive

### /!\ Au premier lancement de Proaxive, il vous sera demandé de confirmer la connexion à votre base de données.

Le compte administrateur par défaut est   
Courriel : **mymail@domain.tld**   
Mot de passe : **admin**  

**Important :** Ne pas oublier de modifier le mot de passe et le courriel du compte via le panel

## Fichier de configuration Proaxive
Le fichier de configuration de l'application **.env** se trouve à la racine de cette dernière

```
APP_NAME="Proaxive"
# Environnement (optionnel)
#APP_ENV=local
APP_AUTHOR=SLabs
# Chemin vers le dossier du thème
APP_THEME=/public/assets/styles/proaxive
# Chemin vers le dossier public
APP_ROOT_PUBLIC=/public
# Erreur WHOOPS
APP_DEBUG=
# Erreur PHP
APP_DEBUG_PHP=
```
APP_NAME = Nom de l'application  
APP_ENV = local (dev) / production (mise en ligne)  
APP_THEME = Chemin du thème  
APP_ROOT_PUBLIC = Chemin du dossier "public"  
APP_AUTHOR = l'auteur de l'application   

## Sécurisation minimum (production)
Afin de sécuriser un peu plus l'application, je vous conseil de renommer la route qui permet de se connecter au panel.

Rendez-vous dans les paramètres (panel admin)

Remplacez **login-dash** par quelque chose de plus personnel.

Si l'application fonctionne correctement, **désactivez l'affichage des erreurs**.

## Configuration de l'envoi des courriels
**Important** Pensez à bien inscrire une adresse courriel dans les informations de votre entreprise  
> Menu "Accueil" puis onglet "Mon entreprise"

Champ "Courriel"  

La configuration des courriels se fait maintenant dans le panel d'administration  
> Menu "Paramètres" puis onglet "Service Courriel"  
### MailJet
Pour Mailjet, il également nécessaire de remplir la partie "Configuration SMTP".   

Adresse courriel : votre adresse courriel ajoutée dans Mailjet   
Utilisateur : votre clé public Mailjet   
Mot de passe : votre clé privée Mailjet  

La configuration des courriels sera "allégée" à l'avenir.  

### Tester la configuration
Rentrez votre adresse courriel de test dans le champ "Courriel de test".  
Afin de s'assurer que votre configuration fonctionne correctement, cliquez sur "Tester l'envoi".
