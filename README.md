<a id="readme-top"></a>
<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/pb402385/first_symfony_project">
    <img src="/public/images/email/logo_docshare.jpg" alt="Logo" width="180" height="80">
  </a>

  <h3 align="center">Démo d'un site de partage de documents entre utilisateurs</h3>
</div>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Sommaire</summary>
  <ol>
    <li>
      <a href="#a-propos-du-projet">A propos du projet</a>
      <ul>
        <li><a href="#demo-video">Démo (vidéo)</a></li>
        <li><a href="#technologies">Technologies</a></li>
      </ul>
    </li>
    <li>
      <a href="#commencer">Commencer</a>
      <ul>
        <li><a href="#prérequis-et-installation">Prérequis et installation</a></li>
      </ul>
    </li>
    <li><a href="#presentation">Presentation de l'application</a></li>
    <li>
      <a href="#code">Presentation de l'architecture du code et diverses informations techniques</a>
      <ul>
        <li><a href="#database">Presentation des tables de la base de données</a></li>
      </ul>
      <ul>
        <li><a href="#architecture">Presentation de l'architecture de l'application</a></li>
      </ul>
    </li>
    <li><a href="#todo">Points à améliorer</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Remerciements</a></li>
  </ol>
</details>

<!-- ABOUT THE PROJECT -->
## A propos du projet
<a id="a-propos-du-projet"></a>

Ce site a pour **objectif** de relier un communauté d'utilisateurs qui pourront une fois inscrits et logués, **déposer des documents** de type PNG/JPG/PDF.

Les documents appartiennent à une catégorie.

Les utilisateurs pourront ensuite **rechercher ces documents** et **mettre une note** et optionnellement un avis pour chaque document consulté.

<ins>Disclaimer:</ins> *C'est le premier projet que je fais en php symfony, je l'ai fait principalement pour disposer d'un template dont je pourrais m'inspirer si je suis amené à faire d'autres sites en php symfony.*
<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Démo vidéo
<a id="demo-video"></a>

<div align="center">
  Voici une démonstration du rendu final de l'application (Cliquez sur l'image pour regarder la vidéo):
</div>
<br/>


[![Miniature de la vidéo](https://img.youtube.com/vi/amGpBCkkSVQ/hqdefault.jpg)](https://youtu.be/amGpBCkkSVQ)

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Technologies
<a id="technologies"></a>

* [TWIG](https://twig.symfony.com/)
* [PHP SYMFONY](https://symfony.com/doc)
* [JavaScript](https://en.wikipedia.org/wiki/JavaScript)
* [HTML](https://en.wikipedia.org/wiki/HTML)
* [CSS](https://en.wikipedia.org/wiki/CSS)


<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Commencer
<a id="commencer"></a>

### Prérequis et installation
<a id="prérequis-et-installation"></a>

1 - Lancer powershell dans la console
```bash
 PowerShell 
```
2 - Télécharger et installer scoop
```bash
 Set-ExecutionPolicy RemoteSigned -scope CurrentUser
 iwr -useb get.scoop.sh | iex
```
3 - Installer le nodejs
```bash
 scoop install nodejs 
```
4 - Installer symfony
```bash
 scoop install symfony-cli 
```
5 - Installer le projet depuis GIT
```bash
git clone <url-du-depot>
cd my_project
```
6 - Installer les dépendances
```bash
composer install
```
7 - Initialiser la base de données (après installation de postgresql18 via l'installeur)
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

J'ai personnèlement eu un souci avec mon xamp qui gère ma partie SQL, la manip pour repartir d'une base de données propre:
D'abord créer la table symfony et ensuite executer ces deux commandes:
```bash
php bin/console doctrine:migrations:migrate --allow-no-migration
php bin/console doctrine:migrations:execute --up DoctrineMigrations\Version20260519112421
```

Uploader le backup de la base de données pour initializer les données (cela crée quelques comptes, un compte admin, quelques documents présents dans le projet à titre d'exemples), le backup se situe dans *migrations/backup_sql* et se nomme symfony.sql et executez le dans votre SQL.
→ Tous les users crées par défaut ont pour mot de passe **test**

**Ne pas oublier de lancer mailpit.exe** qui se situe dans le dossier */bin* via un CMD comme dans l'exemple suivant (cela permet au mails d'être envoyés en local et c'est nécessaire pour le bon déroulement de l'inscription sur l'application, **sinon utiliser votre propre SMTP**, par exemple, moi j'ai utilisé le SMTP de google en plaçant sa config dans .env exemple: #MAILER_DSN=gmail+smtp://email@gmail.com:APP_ID@default)

Voici ce que vous verrez après le lancement de mailpit.exe
```bash
C:\Users\pb402\Desktop\php_test\my_project\bin (main -> origin)
λ mailpit.exe
time="2026/06/28 17:16:24" level=info msg="[smtpd] starting on [::]:1025 (no encryption)"
time="2026/06/28 17:16:24" level=info msg="[cors] allowed API origins: "
time="2026/06/28 17:16:24" level=info msg="[http] starting on [::]:8025"
time="2026/06/28 17:16:24" level=info msg="[http] accessible via http://localhost:8025/"
```

8 - Lancer le serveur:
```bash
symfony server:start
```

Vous pouvez normalement ouvrir votre server sur l'adresse suivante: https://localhost:8000/

Si vous voulez ajouter des catégories de document, connectez vous avec l'admin (login: admin@docshare.fr mdp: test) et allez à l'url suivante: https://localhost:8000/admin/category

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Presentation de l'application
<a id="presentation"></a>

Lors de notre première visite, on arrive sur la page d'accueil

<div align="center">
    <img src="/public/documentation_screenshots/Home.jpg" alt="home.jpg" />
</div>


On est invité soit à se connecter si l'on dispose déjà d'un compte, sinon à s'inscrire.Comme il s'agit de notre première connexion on se dirige sur la page d'inscription.

<div align="center">
    <img src="/public/documentation_screenshots/Register.jpg" alt="register.jpg" />
</div>

Une fois notre formulaire envoyé, on reçoit un message de confirmation qui nous invite à nous rendre sur notre email afin de vérifier celui-ci pour finaliser notre inscription.

<div align="center">
    <img src="/public/documentation_screenshots/Register-ok.jpg" alt="register-ok.jpg" />
</div>

Sur l'email de confirmation de l'email, on nous invite à cliquer sur un lien qui nous permettra de vérifier qu'il s'agit bien d'un email valide. 

<div align="center">
    <img src="/public/documentation_screenshots/Register-activation-mail.jpg" alt="register-activation-mail.jpg" />
</div>

Notre inscription est désormais valide et l'on peut désormais se connecter à notre application

<div align="center">
    <img src="/public/documentation_screenshots/Register-activation-mail-ok.jpg" alt="register-activation-mail-ok.jpg" />
</div>

Une fois connecté, on arrive sur la page d'accueil avec un message de confirmation et l'on peut désormais accéder à notre profil ou celui des autres utilisateurs ainsi qu'aux documents déposés par les utilisateurs

<div align="center">
    <img src="/public/documentation_screenshots/login-ok.jpg" alt="login-ok.jpg" />
</div>

De plus notre page d'accueil nous affiche désormais un diagramme camembert pour nous representer le nombre de documents par catégorie

<div align="center">
    <img src="/public/documentation_screenshots/Home-si-connecte.jpg" alt="Home-si-connecte.jpg" />
</div>

Commençons par accéder à notre profil (tel qu'il apparait pour un nouvel utilisateur qui vient tout juste de s'inscrire)

<div align="center">
    <img src="/public/documentation_screenshots/Profil-base.jpg" alt="Profil-base.jpg" />
</div>

Maintenant regardont à quoi ressemble le profil d'un utilisateur qui a déjà déposé des document ainsi que noté et commenté des documents d'autres utilisateurs.

Tout d'abord la partie des informations qui le concerne 
<div align="center">
    <img src="/public/documentation_screenshots/user-profile-exemple-p1.jpg" alt="user-profile-exemple-p1.jpg" />
</div>

Ensuite la partie concernant les derniers documents qu'il a déposé
<div align="center">
    <img src="/public/documentation_screenshots/user-profile-exemple-p2.jpg" alt="user-profile-exemple-p2.jpg" />
</div>

Enfin la partie concernant les derniers avis (note/commentaires) qu'il a déposé
<div align="center">
    <img src="/public/documentation_screenshots/user-profile-exemple-p3.jpg" alt="user-profile-exemple-p3.jpg" />
</div>


On peut également consulter les profils des autres utilisateurs en cliquant sur le bouton Utilisateurs du menu du header (On a deux affichages disponibles, par CARDS ou dans un tableau ainsi que la possibilité de faire des recherches par nom, email ou pays)

<div align="center">
    <img src="/public/documentation_screenshots/Users-cards.jpg" alt="Users-cards.jpg" />
</div>

Maintenant en ce qui concerne la partie Documents de l'application, on arrive sur une page qui référence tous les documents déposés par les utilisateurs que l'on peut afficher comme pour les utilisateurs, soit par cards, soit dans un tableau (on peut les filtrer par leur titre ou catégories ainsi que par le nom ou l'email de l'utilisateur qui les a déposé sur l'application )

<div align="center">
    <img src="/public/documentation_screenshots/Documents-cards.jpg" alt="Documents-cards.jpg" />
</div>

Le bouton créer un document permet d'ajouter un document à l'application, une fois le document ajouté, on reçoit un email pour nous notifier de la bonne réception du document et l'on génère un PDF qui certifie notre dépôt (une sorte de preuve de dépôt du document)

<div align="center">
    <img src="/public/documentation_screenshots/Mail-depot-document.jpg" alt="Users-cards.jpg" />
</div>

<div align="center">
    <img src="/public/documentation_screenshots/PDF-depot-document.jpg" alt="Mail-depot-document.jpg" />
</div>


Nos document sont ensuite injectés dans le file system dans le dossier public/upload/documents pour le document et dans le dossier public/upload/documents/receipts pour la preuve de dépôt du dit document

<div align="center">
    <img src="/public/documentation_screenshots/Gestion-du-FS.jpg" alt="Gestion-du-FS.jpg" />
</div>

Voici la page qui nous permet d'afficher le contenu d'un document (on peut y voir l'image ou la visualisation du PDF ainsi que les diverses informations du document comme son titre, son résumé, sa catégorie, sa date de création, son auteur et sa note ) et l'on peut également le télécharger


<div align="center">
    <img src="/public/documentation_screenshots/Document-show-b.jpg" alt="Document-show-b.jpg" />
</div>

Un document peut être noté et commenté par un utilisateur (un utiliateur ne peut noter et commenter qu'une seule fois par document) et l'on peut consulter les notes ainsi que tous les commentaires du dit document sur cette page

<div align="center">
    <img src="/public/documentation_screenshots/Document-all-avis.jpg" alt="Document-all-avis.jpg" />
</div>


Enfin il reste une page qui permet de contacter le support technique (accessible via le footer du site)

<div align="center">
    <img src="/public/documentation_screenshots/Contact.jpg" alt="Contact.jpg" />
</div>

Une fois le formulaire soumis un email est envoyé au support technique

<div align="center">
    <img src="/public/documentation_screenshots/Mail-exemple-de-contact-support.jpg" alt="Mail-exemple-de-contact-support.jpg" />
</div>

Voilà le tour général de la présentation de l'application est terminé!


<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Presentation de l'architecture du code et diverses informations techniques
<a id="code"></a>

#### Presentation des tables de la base de données
<a id="database"></a>

Tout d'abord j'aimerai juste présenter les tables de la base de données
Nous avons 4 tables importantes:
La table user qui contient nos utilisateurs
La table catégorie qui contient la liste de nos catégories
La table document qui contient nos documents
La table note qui contient nos notes

Un utilisateur peut avoir n documents
Un utilisateur peut avoir une note par document
Un document doit avoir une catégorie

les tables doctrine_migration_versions (la table de base de données par défaut utilisée par Doctrine Migrations) et messenger_messages (elle sert de file d'attente native pour stocker les messages asynchrones avant leur traitement par un worker) sont crées automatiquement par symfony php

Enfin il reste une table revoked_token que j'ai crée car j'ai anticipé des besoins futurs et je génère un token JWT qui pourra me servir dans le futur à détecter une connexion valide pour que mon application puisse consommer des services REST dont l'origine ne serait pas le site web, cette table révoke le token, on a une commande que l'on peut executer afin de cleaner les tokens. Idéalement à terme il faudrait y mettre un CRON (tâche récurrente) qui s'executerait tous les jours mais j'ai laissé cette partie pour plus tard car actuellement le JWT token ne m'est d'aucune utilité car symfony gère son propre système de token sur le site web.

<div align="center">
    <img src="/public/documentation_screenshots/Database.jpg" alt="Database.jpg" />
</div>


<p align="right">(<a href="#readme-top">back to top</a>)</p>



#### Presentation de l'architecture de l'application
<a id="architecture"></a>

En ce qui concerne l'architecture, voici une courte explication des principaux dossiers:

assets/js/AuthManager.js -> fichier Auth manager qui va m'aider à gérer la session JWT
assets/css -> CSS de l'application

bin -> Dossier contenant Mailpit (un outil de test d'emails et de serveur SMTP local)

config -> Contient le cœur de la configuration de votre projet. Il stocke la configuration de chaque paquet (bundle) installé, contient également nos deux clés (privée et publique) dans le dossier jwt nécessaires au token JWT, contient également nos packages qui nous permettent de gérer la configuration principale

migrations -> Contient les migrations nécessaires pour la mise en place de la base de donnée ainsi que le dossier backup_sql (utile pour mettre en place un jeux de données de test)

public -> Contient les images du sites et de la documentation ainsi que qu'un dossier upload qui est notre file system des documents et preuves de dépôt du projet. Contient également adminer.php qui nous permet de visualiser/ajouter nos données SQL (on peut également utiliser XAMPP pour visualiser/ajouter des données manuellement) 

src/Command -> ce dossier nous permet d'executer des commandes via la console sur notre projet (Actuellement il contient notre commande qui permet d'effacer nos tokens revokés).Pour executer la commande:
```bash
php bin/console app:clean-revoked-tokens
```

src/Controller -> C'est l'emplacement par défaut où résident les classes de contrôleurs dans l'application. Ces classes gèrent les requêtes HTTP et retournent les réponses.

src/DTO -> C'est ici que l'on stocke les Data Transfer Objects (Objets de Transfert de Données). Ces objets servent à découpler la structure de vos données internes (Entités Doctrine) de la structure exposée ou attendue par l'extérieur (API, formulaires).

src/Entity -> Contient les classes qui représentent nos données persistantes (tables de la base de données) via l'ORM Doctrine. C'est le modèle de données de l'application qui contient nos entités dans lequel on définit nos clés primaires, nos contraintes ainsi que nos relations relations (OneToOne, OneToMany, ManyToMany, ...).

src/Form -> Contient les classes de types de formulaire (Form Types) qui définissent la structure, les champs et la validation de vos formulaires.

src/Repository -> Ccontient les classes responsables de la récupération des données depuis la base de données pour une entité spécifique. Chaque repository étend ServiceEntityRepository, ce qui permet de l'injecter directement comme un service. On a 1 fichier par table qui nous permettent de gérer nos requêtes SQL.

src/Security ->  regroupe les classes personnalisées liées à l'authentification et à l'autorisation dans une application Symfony. Bien que la configuration principale se trouve dans config/packages/security.yaml, ce dossier contient la logique métier spécifique que la configuration ne peut pas couvrir. On y gère actuellement du code lié à l'authentification.

src/Service -> C'est ici que réside la logique métier, actuellement on a principalement deux services utilisés, le premier (MailService.php) concerne les envois d'emails et le second (DocumentReceiptService.php) nous permet de générer un PDF qui nous sert de preuve d reception d'un document

src/Twig -> contient les classes qui étendent les fonctionnalités du moteur de template Twig dans Symfony. Il peut être utilisé pour créer des filtres, des fonctions, des tests personnalisés et des composants réutilisables disponibles dans toutes nos vues. Actuellement on a deux classes, la première Base64Extension.php nous permet d’utiliser la fonction base64_encode directement dans les templates Twig, ce qui est pratique pour générer des URLs data:base64 et la seconde CountryExtension.php qui nous permet de récupérer le nom du pays ainsi que son icône de drapeau en fonction du code du pays (en, fr, ...)


src/Validator -> Ce dossier est dédié à la création de règles de validation métier personnalisées qui ne sont pas couvertes par les contraintes standards de Symfony. Actuellement il nous permet de bannir des mots dans les email, par exemple j'interdis le terme "yopmail" dans les emails lors de la création du compte

templates -> Ce dossier contient tous nos templates Twig dans lequel on peut injecter nos données (contient un dossier component qui sont des templates que j'utilise dans d'autres templates). Concernant les templates on a ici les templates de nos pages web mais également de nos emails et de nos PDF générés par notre application.

.env -> C'est le point central de configuration des variables d'environnement. Il permet de définir des paramètres sensibles (mots de passe, clés API) et des configurations spécifiques à chaque environnement (dev, prod, test) sans les coder en dur. En cas de developpement local, il faudra y référencer l'addresse de la base de données dans DATABASE_URL ainsi que le DSN du mailer dans MAILER_DSN

<div align="center">
    <img src="/public/documentation_screenshots/architecture.jpg" alt="architecture.jpg" />
</div>

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Points à améliorer
<a id="todo"></a>

Voici quelques point à améliorer dès que j'aurai un peu de temps:
- Tester le backup SQL sur une fresh install du projet et vérifier que tout fonctionne correctement
- Faire le CSS responsive du site
- Le diagramme camembert ne s'affiche pas après l'inscription ou la connexion, il faut actuellement raffraichir la page d'accueil pour qu'il apparaisse (mini bug à corriger)
- Partie catégorie, la page pour les ajouter existe mais elle est cachée et accessible que par l'administrateur donc idéalement rajouter un bouton pour pouvoir facilement ajouter ou supprimer une catégorie mais attention car la catégorie est obligatoire pour un document donc la suppression de catégorie impliquera de réaliser une opération sur tous les documents de la dite catégorie! 

<p align="right">(<a href="#readme-top">back to top</a>)</p>


## License
<a id="license"></a>

Le projet est entièrement **open source et gratuit**. Aucune restriction: Simplement du code libre au service de tous, offert à la communauté.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Contact
<a id="contact"></a>

Informations de contact:

**Nom**: Porta <br/>
**Prénom**: Benjamin <br/>
**Pays**: FRANCE <br/>
**Ville**: Nice <br/>
**E-mail**: pb402385@gmail.com <br/>
**Github**: https://github.com/pb402385

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Remerciements
<a id="acknowledgments"></a>

En ce qui concerne les remerciements, je souhaiterai remercier ***grafikart*** pour ces tutoriels que j'ai consulté sur Youtube (https://www.youtube.com/@grafikart), ils m'ont énormément aidé à bien débuter!

J'aimerai également remercier GROK, qui m'a énormément soulagé ma charge de travail :)


<p align="right">(<a href="#readme-top">back to top</a>)</p>
