# INSTALLATION DU SERVER:

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
5 - Créer son projet (attention cmder à lancer en mode admin sinon scoop plante -> lol un peu de la merde quand même...)

si on souhaite une app web ( ce que je vais tester dans mon cas )
```bash
 symfony new --webapp my_project 
```
si on veut plutôt un microservice, une application console ou une API:
```bash
 symfony new my_project 
```

6 - mettre le php.exe dans les variables d'environnement et dans le vscode(gestion de la console)
7 - installer composer -> https://getcomposer.org/download/


# LANCEMENT DU SERVER:

Pour lancer:
```bash
 symfony server:start 
```
Pour stopper:
```bash
 symfony server:stop 
```
Ne pas oublier de lancer mailpit.exe qui se situe dans le dossier /bin (cela permet au mails d'être envoyés en local et c'est nécessaire pour le bon déroulement de l'inscription sur l'application, on peut sinon utiliser le SMTP de google en plaçant sa config dans .env exemple: #MAILER_DSN=gmail+smtp://email@gmail.com:APP_ID@default)


# DEBUG (utile au début)

Lister toutes les routes
```bash
php bin/console debug:router

php bin/console debug:router --show-aliases
php bin/console debug:router --show-controllers
php bin/console debug:router --method=GET
php bin/console debug:router --method=ANY
```

Pass the name (or part of the name) of some route to this argument to print the route details:
```bash
php bin/console debug:router app_lucky_number
```
The other command is called router:match and it shows which route will match the given URL. It's useful to find out why some URL is not executing the controller action that you expect:
```bash
php bin/console router:match /lucky/number/8
> [OK] Route "app_lucky_number" matches
```

# DATABASE

installation de postgresql18 via l'installeur


# DOC LIENS UTILES:

- arboressance:
config/
Contains configuration. You will configure routes, services and packages.

- src/
All your PHP code lives here.

- templates/
All your Twig templates live here.

- bin/
The famous bin/console file lives here (and other, less important executable files).

- var/
This is where automatically-created files are stored, like cache files (var/cache/) and logs (var/log/).

- vendor/
Third-party (i.e. "vendor") libraries live here! These are downloaded via the Composer package manager.

- public/
This is the document root for your project: you put any publicly accessible files here.

Start: https://symfony.com/doc/7.4/page_creation.html
Rooting: https://symfony.com/doc/7.4/routing.html
Symfony forms: https://symfony.com/doc/7.4/forms.html

Console commandes: https://symfony.com/doc/7.4/console.html#console-completion-setup
Install packages: https://symfony.com/doc/7.4/setup.html
Symfony and HTTP Fundamentals: https://symfony.com/doc/7.4/introduction/http_fundamentals.html


# CMD utiles:

- creation d'un controller automatiquement:
```bash
 php bin/console make:controller UserController
```

 - creation entite (utile pour la creation d'objet BDD)
```bash
 php bin/console make:entity
```


 - pour creer le sql
```bash
php bin/console make:migration
```

 - pour executer le sql dans la base de données
```bash
 php bin/console migrate
 doctrine:migrations:migrate
 
 # cette commande peut mieux fonctionner
 php bin/console doctrine:migrations:migrate
```
- pour annuler la dernière migration en cas d'erreur (par exemple dans mon cas j'avais ajouté 2 colonnes à la mauvaise table, par contre attention j'ai du aller corriger l'entité moi même car je n'ai pas supprimé et regénéré l'entité)
```bash
symfony console doctrine:migrations:migrate prev --no-interaction
```

 ensuite on rajoute le Repo à notre controller: exemple:
```php
 use App\Repository\UserRepository;
```
 puis on l'objet repo a la methode souhaitée
```php
 public function index(Request $request, UserRepository $repository): Response
``` 
-> on obtient l'objet
```php
 dd($repository->findAll());
 ```
 -> exemple avec un critere:
```php
 $user = $repository->find($id);
 ```
 ou avec critère (attention a la flèche qui n'est as -> mais =>)
```php
 $user = $repository->findOneBy(['email' => $email]);
```
si on a besoin d'une requête plus complexe, on la construit dans le repository
```php
 public function findByBirthdateSuperiorAt($value): array
    {

        //$formated_date = $value->format('Y-m-d');
        return $this->createQueryBuilder('u')
            ->where('u.bornAt > :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
```
pour tester:
```php
 $dateTest1 = '1985-03-18 00:00:00';
 $dateTest2 = '1989-03-18 00:00:00';
 dd($repository->findByBirthdateSuperiorAt($dateTest1),$repository->findByBirthdateSuperiorAt($dateTest2));
```

pour ajouter des données, on rajoute le entity manager interface à notre contrôleur.
On peut modifier nos donnnées à la volée de cette façon avec un flush
```php
$users[0]->setAbout("Je me présente,
                     Je m'appelle Henri,
                     Je voudrais bien réussir ma vie,
                     Être aimé,
                     Être beau, gagner de l'argent,
                     Puis surtout être intelligent,
                     Et pour tout ca, il faudrait que j'bosse a plein temp");
$em->flush();
```
On peut récupérer le repository directement depuis l'entity manager, ça évite d'injecter trop de choses dans le contrôleur
```php
$users = $em->getRepository(User::class)->findAll();
        // = $repository->findAll()
```

Creation de formulaires: (Type par convention )
```bash
 php bin/console make:form
 
 UserType
 
 User
```

Pour surcharger une méthode, par exemple forcer le deleter via un champs type hidden, on doit modifier la config dans framework.yaml
```yaml
framework:
secret: '%env(APP_SECRET)%'

    # Note that the session will be started ONLY if you read or write from it.
    session: true

    #esi: true
    #fragments: true
    http_method_override: true
```
Puis on peut faire fonctionner note action DELETE
```html
<form action="{{ path('user.delete', {id:user.id}) }}" method="post">
     <input type="hidden" name="_method" value="DELETE">
     <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
</form>
```

En ce qui concerne les Validations, on peut les écrire dans le UserType via les PRESUBMIT ET POSTSUBMIT mais on peut également directement les écrire dans les entity
```php
#[ORM\Column(length: 255)]
#[Assert\Length(min: 5)]
#[Assert\Email]
#[ASSERT\NotBlank]
private ?string $email = null;
```

On peut également créer des Validators personnalisés
```bash
 php bin/console make:validator
```
voir banwordphp et BanWordPhpValidator il permet de banir un terme c'est un exemple de ce que l'on peut faire

Ajout de dépendance avec le composer, par exemple pour ajouter de SMTP de gmail, on rentre ceci dans la console dans powershell (puis on met à jour les conf dans le .env)
```bash
 composer require symfony/google-mailer
```

CSRF Manuel (quand tu n’utilises pas Symfony Forms)
```twig
{% for article in articles %}
    <form method="POST" action="{{ path('article_delete', {id: article.id}) }}" style="display:inline;">
        <input type="hidden" name="_csrf_token" value="{{ csrf_token('delete_article_' ~ article.id) }}">
        <button type="submit" class="btn btn-danger btn-sm"
            onclick="return confirm('Supprimer cet article ?')">
            Supprimer
        </button>
    </form>
{% endfor %}
```
et on place dans le contrôleur une vérification manuelle
```php
#[Route('/article/{id}/delete', name: 'article_delete', methods: ['POST'])]
public function delete(Request $request, Article $article, CsrfTokenManagerInterface $csrfManager): Response
{

    // recuperation du token si c'est celui injecté par twig automatiquement (mai normalement twig gère bien cette partie si on utilise le form de symfony 
    // $token = $request->request->all()['document']['_token'];

    // si c'est nous qui l'ajoutons
    // $request->request->get('_csrf_token')

    // Validation manuelle du token
    if (!$this->isCsrfTokenValid('delete_article_' . $article->getId(), $request->request->get('_csrf_token'))) {
        throw $this->createAccessDeniedException('Token CSRF invalide.');
    }

    // ... suppression
}

```

# NOTES utiles:

- La configuration passe par le Kernel, en cas de changement de dépendances s'assurer que:
```text
Make sure that your previous configuration files don't have imports declarations pointing to resources already loaded by Kernel::configureContainer() or Kernel::configureRoutes() methods.
```
- on a les fichiers composer et symphony qui servent à gérer les dépendances (ça ressemble à package.json de angular) 

-methode dd utile -> fait un var_dump amélioré avec un die (dump fait de même sans le die)

- généer les clés pour l'auth par token, dans config/jwt
```bash
openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

Pour créer un code qui s'execute grâce à une commande
```bash
php bin/console make:command CleanRevokedTokensCommand
```
Pour executer la commande que l'on vient de créer
```bash
php bin/console app:clean-revoked-tokens
```

# TODO


+ Page accueil à finaliser
+ Gerer session user/admin pour les deux pages user et document
  - idéalement l'user peut delete son compte (voir quelles implications, si on garde les docs ou si on delete tout par cascade

    
+ liste des documents uploadés sur la partie profil
+ visionneuse pour les documents
+ 
+ idéalement pouvoir noter un document
+ idealement pouvoir mettre un document favori
