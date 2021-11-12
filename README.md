# Izy-mvc

![Izy-mvc, framework PHP](https://izy-mv.com/assets/im/brand.svg)

![Compatible Php 7 & 8](https://img.shields.io/badge/Compatible%20Php-7%20&%208-blue)

[www.izy-mvc.com](https://www.izy-mvc.com)

Un framework PHP simple, léger et puissant spécialement conçu pour les **débutants en PHP**.

## Une alternative aux frameworks professionnels

Vous avez un projet de création de site internet ou d'application web mais vous ne savez pas par où commencer, faute de compétences techniques ? CakePHP et Symfony vous semblent trop lourds et vous n'avez pas envie de passer du temps à les apprendre ?

Alors, si vous disposez d'**un minimum de connaissance en PHP** et que vous souhaitez les mettre en pratique pour réaliser rapidement un projet web, **Izy-mvc est fait pour vous** !

Inspiré de CodeIgniter, Izy-mvc est un framework PHP conçu sur le modèle de conception **MVC** (Modèle, Vue, Contrôleur), facile à configurer et à prendre en main. Concentrez-vous sur le contenu, il se chargera du reste !

### Une configuration minimale

Je pars du principe que vous disposez au minimum d'un serveur web local (du type [WampServer](https://www.wampserver.com/) par exemple), chose indispensable pour pouvoir tester votre travail en local avant sa mise en ligne sur un serveur distant.

La seule chose que vous ayez à faire pour démarrer votre projet de création de site web est alors de renseigner l'URL de votre site dans le fichier `config/config.php` :

```php
$config['host'] = $_SERVER['HTTP_HOST'] == 'localhost' ? 'localhost/mon-site' : 'www.mon-site.com';
```
C'est tout ! Vous serez alors en mesure de créer vos premiers contrôleurs et vos premières vues pour donner vie à votre projet.

## Un framework simple mais puissant

Bien qu'il soit avant tout conçu pour les personnes débutantes, Izy-mvc conviendra également aux **utilisateurs plus exigeants**, leur permettant par exemple de gérer leurs dépendances installées à laide de **Composer**.

Ils apprécieront également son **système de routage des URL** permettant de modifier le comportement par défaut du framework, basé sur le principe suivant :

```html
http://domain.com/class/method/argument
```
Izy-mvc permet également d'**étendre les classes du système**, afin qu'elles puissent pleinement répondre aux besoins spécifiques de votre application.

Enfin, après avoir paramétrer la connexion à un serveur de base de données, vous serez en mesure de communiquer avec ce dernier en ayant recours à l'**objet PDO**.

## Une documentation en français claire et complète

Avant de vous lancer dans la création de votre projet, je vous conseille dans un premier temps de parcourir la [documentation en ligne](https://www.izy-mvc.com/userguide/introduction).

Elle permettra de répondre à vos éventuelles interrogations concernant les possibilités offertes par Izy-mvc.