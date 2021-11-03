#### Proaxive 1.6.0-beta6

```
Refactoring config database
Correction HTML/CSS
```

#### Proaxive 1.6.0-beta5

```
Correction export society
Correction navigation
```

#### Proaxive 1.6.0-beta4

```
Ajout de l'export Society (CSV)
Ajout d'API Adresse (Client, Society)
Intégration d'OpenStreetMap dans les fiches clientes
Intégration d'un lien Waze dans les fiches clientes
Correction du message d'erreur si contenu de la note est vide
Ajout d'un bouton pour supprimer une note
Correction de l'affichage des statuts
```

#### Proaxive 1.6.0-beta3

```
Correction suppression intervention
Ajout des notes épinglées en accueil
Correction du fichier .sql de mise à jour
Correction de $textcolor si null (Status)
Correction des notifications (stickies)
Correction des liens (tuiles)
Corrections CSS
Redirection après avoir cliqué sur "Débuter l'intervention"
```

#### Proaxive 1.6.0-beta2

```
Mise à jour du thème
Correction de l'erreur d'ajout d'équipement
Correctios des fautes d'orthographe
Ajout RGBA pour les couleurs de fond des statuts
Correction Topbar flottante dans les paramètres
Correction impression des inters/débours
Correction responsive (accueil du tracker)
Correction responsive sur les tablettes
Correction des notes épinglées
Amélioration graphique de la liste des notes
Ajout d'une Lighbox dans la galerie (interventions)
Correction CSS dans le template Mail
Ajout du model Sociéty (lié aux Clients)
Mise à jour de la base de données (à lancer via le panel)
```

#### Proaxive 1.6.0.1 (beta)
Cette version propose un redesign complet de l'application. Le framework CSS Spectre se voit remplacé par une simple grille CSS nommée Gridlex.  
Un système de note interne est maintenant disponible dans le panel d'administration.  
J'ai également apporté plusieurs améliorations.

La dénomination *Lite* de Proaxive n'existe plus.

```
- Refonte totale de la charte graphique
- Ajout de la gestion utilisateur
- Ajout de la section "Mon Compte"
- Gestion des utilisateurs par rôle
- Réorganisation des paramètres
- Ajout d'un système de note interne
- Gestion des utilisateurs pour les interventions/équipements/débours
- Suppression du framework CSS Spectre
- Un seul style pour toute l'application.
- Remplacement d'IcoFont par Remix Icon
- Mise à jour des Débours (ajout technicien)
- Ajout des interventions dans les débours (sélection par client)
- Ajout d'une galerie photos pour les interventions
- Mise à jour Equipment (Rapport équipement BAO)
- Mise à jour des routes
- Affichage des erreurs dans la partie "Services courriel"
- une alerte s'affiche si les erreurs Whoops sont activées
- Ajout de la sauvegarde de base de données depuis le panel
- Gestion de la base de donnée (PWS uniquement)
- Correction du mot de passe trop long
- Il n'est plus possible de modifier/ajouter des départements (aucune utilité à mon sens)
- Mise à jour des packages (vendor)
```

#### Proaxive Lite 1.5.2 (stable)
Version stable du projet Proaxive.
```
- Ajout de la gestion de débours
- Suppression card sécurtié application (paramètres)
- Corrections HTML/CSS mobile
- Correction du menu (mobile)
- Mise à jour "Services Courriel"
- Mise à jour profil client
```
#### [Fichiers nouveaux/édités]
config/routing.php  
app/Controller/Dashboard/AdminOutlayController.php  
app/Controller/Dashboard/AdminClientController.php  
app/Entity/OutlayEntity.php  
app/Model/OutlayModel.php  
views/outlay/*all  
views/_layout/dashboard/_main_menu.twig  
views/_layout/dashboard/_main_menu_mobile.twig
views/client/admin/show.twig
views/_layout/dashboard/layout.twig  
public/assets/styles/admin-default/stylesheets/layout.css  
public/assets/styles/print-intervention/voucher.css
views/dashboard/settings/courriel.twig  
views/dashboard/settings/home.twig  
views/templates/print_outlay.twig
app/Controller/Dashboard/AdminSettingController.php  
src/MyClass/SendMail.php

#### Proaxive Lite 1.5.1 (stable)
Première version stable du projet Proaxive.
```
- Ajout de la suppression client
```
#### [Fichiers édités]   
app/App.php  
config/routing.php  
views/clients/admin/show.twig  
version.xml  
README.md  
composer.json