## Doc

https://symfony.com/doc/5.4/doctrine.html#installing-doctrine

## installation

- inititalisation skeleton
```
symfony new myProject
```

- installation debug bar
```
composer req --dev debug
```
- installer doctrine et le maker bundle
```
composer require symfony/orm-pack
```
```
composer require --dev symfony/maker-bundle
```
- créer un fichier .env.local et configurer la BDD MariaDB ou Sql
```
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=mariadb-10.5.8"
```
```
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
```
- créer la BDD
```
bin/console doctrine:database:create
```
- installer le fixture Bundle Doctrine
```
composer require --dev orm-fixtures
```
- installer Faker pour améliorer les fixture
- https://fakerphp.github.io/
```
composer require --dev fakerphp/faker
```
- installer Stof Doctrine Extension bundle pour automatiser la création de date, slugs directement dans les annotation sur les propriétés des Entités
- https://symfony.com/bundles/StofDoctrineExtensionsBundle/current/index.html
- https://github.com/doctrine-extensions/DoctrineExtensions/tree/main/doc
```
composer require stof/doctrine-extensions-bundle
```
- installer le securityBudle pour gérer l'authentification
```
composer require symfony/security-bundle
```
- installer la validation pour utiliser les Assert eg : unique ou NotBlank... sur les propriétés des Entités
- ajouter le use : use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
- et sur la propriét: @ORM\Column(type="string", length=50, unique=true)
- Regarder dans l'Entity User si besoin...
```
composer require symfony/validator
```
- Creer un user
- https://symfony.com/doc/5.4/security.html
```
bin/console make:user
```
```
bin/console make:migration
```
```
bin/console doctrine:migrations:migrate
```
  
- Gérer l'authentification et créer le controller + le LogInform + l'authenticator
```
bin/console make:auth
```
- Installer la vérification du mail
```
composer require symfonycasts/verify-email-bundle
```
- Installer la génération de formulaire 
```
composer require form
```

- Créer le form d'enregistrement du User
```
bin/console make:registration-form
```
- Installer Mailer pour gérer la confirmation par mail et l'envoie de mail
```
composer require symfony/mailer
```
- Mettre à jour le .env.local avec une config MailTrap pour test en dev
  
```
###> symfony/mailer ###
MAILER_DSN=null://palcer le lien ici
###< symfony/mailer ###
```  
- gérer les Assets (car pas de webpack)
```
composer require symfony/asset
```
```
composer require symfony/twig-bundle
```


## compléments
- vérifier les dépendances compromises :
```
symfony check:security
```
