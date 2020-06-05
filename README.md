# Laravel Notes API Sanctum

L’objectif est d’écrire et de mettre en production une API HTTP de gestion de notes personnelles. Cette API sera le back-end d’une application mobile (l'application mobile n'est pas à rendre).

## Cahier des charges

Afin de vérifier son bon fonctionnement, ainsi que le respect du cahier des charges et des critères d’évaluation fournis ci-dessous, le code source rendu sera exécuté et vérifié par une suite de tests automatisés écrits par l’enseignant.

> Attention : Sachant que ces fonctionnalités seront vérifiées par des tests automatisés, merci de respecter ces spécifications à la lettre. Ceci inclut notamment : le nom des routes, la structure des objets JSON à produire, les chaines de caractères fournies…

## Spécifications fonctionnelles

Lors de son utilisation par une application cliente, l’API à fournir devra permettre à chaque utilisateur de l’application de :
- Créer un compte en fournissant un identifiant et mot de passe Se connecter à l’aide de son identifiant et mot de passe
- Retrouver ses notes, dans l’ordre anti-chronologique, avec leur date de création et de mise à jour
- Afficher une note
- Créer une nouvelle note Modifier une note Supprimer une note

Les notes seront à stocker en tant que texte brut (c.a.d. non HTML) et doivent pouvoir contenir des sauts de ligne, ainsi que n’importe quel caractère Unicode. (accents, emoji…)

## Spécifications techniques

### Modèle de données**

Toutes les données manipulées par l’API doivent être stockées dans une base de données.
- Vous devez créer la table `notes` qui contient toutes les notes.
- Vous devez utiliser la table `users` qui contient tous les utilisateurs pour
l'authentification sur l'application web.
- Vous devez utiliser la table `personal_access_token` qui contient tous les
personal access tokens des utilisateurs pour l'authentification via l'API

### Schéma de la table notes

Chaque `note` doit contenir les propriétés suivantes :
- `id` (type : integer) : identifiant unique de la note, généré automatiquement lors de
l’insertion
- `user_id` (type: int) : identifiant unique de l'utilisateur qui a créé la note
- `content` (type : string) : contenu textuel de la note
- `created_at` (type : date): date et heure à laquelle la note a été créé
- `updated_at` (type : date): date et heure à laquelle la note a été mise à jour pour la dernière fois.
  
Vous devez créer une migration pour la création de cette table dans votre base de données.

### Authentification des utilisateurs

L’API doit être state-less. C’est à dire qu’elle ne nécessite pas l’usage de sessions.

Au lieu de cela, l’identification des utilisateurs sera assurée par l’usage de jetons Personal Access Token (SWT : Simple Web Tokens) grâce au package [Laravel Sanctum](https://laravel.com/docs/7.x/sanctum).

Afin de vérifier l’identité de l’utilisateur derrière chaque appel à l’API, celle-ci devra :
- émettre un jeton Personal Access Token lorsque l’utilisateur s’identifiera
- vérifier la validité du jeton au format `Bearer` dans le header HTTP
`Authorization` de chaque requête.

### Interfaces

Les routes doivent être capables d’extraire les paramètres passés dans le corps de chaque requête au format `application/json` .

La réponse envoyée par chacune de ces routes doit aussi être au format JSON.

Les propriétés de la réponse JSON sont spécifiées dans chaque route à implémenter, tel
que décrites ci-dessous :
