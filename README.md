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
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Remerciements</a></li>
  </ol>
</details>

<!-- ABOUT THE PROJECT -->
## A propos du projet
<a id="a-propos-du-projet"></a>

Ce site a pour objectif de relier un communauté d'utilisateurs qui pourront une fois inscrits et logués, déposer des documents de type PNG/JPG/PDF.

Les documents appartiennent à une catégorie.

Les utilisateurs pourront ensuite rechercher ces documents et mettre une note et optionnellement un avis pour chaque document consulté.
<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Démo vidéo
<a id="demo-video"></a>

Voici une démonstration du rendu final de l'application (Cliquez sur l'image pour regarder la vidéo):

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
8 - Lancer le serveur:
```bash
symfony server:start
```

Vous pouvez normalement ouvrir votre server sur l'adresse suivante: localhost:8000

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Presentation de l'application
<a id="presentation"></a>









TODO













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
