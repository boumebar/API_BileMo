# BileMo #

[![Maintainability](https://api.codeclimate.com/v1/badges/dab395fa3a38eafca059/maintainability)](https://codeclimate.com/github/boumebar/API_BileMo/maintainability)

Formation ***Développeur d'application - PHP / Symfony***.  

## Informations du projet ##
Créez un web service exposant une API

### Besoin client
Le premier client a enfin signé un contrat de partenariat avec BileMo ! C’est le branle-bas de combat pour répondre aux besoins de ce premier client qui va permettre de mettre en place l’ensemble des API et de les éprouver tout de suite.

 Après une réunion dense avec le client, il a été identifié un certain nombre d’informations. Il doit être possible de :

consulter la liste des produits BileMo ;
consulter les détails d’un produit BileMo ;
consulter la liste des utilisateurs inscrits liés à un client sur le site web ;
consulter le détail d’un utilisateur inscrit lié à un client ;
ajouter un nouvel utilisateur lié à un client ;
supprimer un utilisateur ajouté par un client.
Seuls les clients référencés peuvent accéder aux API. Les clients de l’API doivent être authentifiés via OAuth ou JWT.

## Installation ##

1. Clonez ou téléchargez le repository GitHub :
```
    git clone https://github.com/boumebar/API_BileMo.git
```
2. Configurer vos variables d'environnement dans le fichier .env .
3. Téléchargez et installez les dépendances du projet avec [Composer](https://getcomposer.org/download/) :
```
    composer install
```
4. Creer votre base de données
```
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:migrations:migrate
```
5. Installer vos fixtures
```
    $ php bin/console doctrine:fixtures:load
```
6.Générer vos clés SSH 
```
    $ mkdir -p config/jwt
    $ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    $ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

## Félicitation l'installation est terminée , vous pouvez tester votre API  
