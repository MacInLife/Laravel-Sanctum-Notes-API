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
- Créer une nouvelle note 
- Modifier une note 
- Supprimer une note

Les notes seront à stocker en tant que texte brut (c.a.d. non HTML) et doivent pouvoir contenir des sauts de ligne, ainsi que n’importe quel caractère Unicode. (accents, emoji…)

## Spécifications techniques

### Modèle de données

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
- vérifier la validité du jeton au format `Bearer Token` dans le header HTTP
`Authorization` de chaque requête.

### Interfaces

Les routes doivent être capables d’extraire les paramètres passés dans le corps de chaque requête au format `application/json` .

La réponse envoyée par chacune de ces routes doit aussi être au format JSON.

Les propriétés de la réponse JSON sont spécifiées dans chaque route à implémenter, tel
que décrites ci-dessous :

### Route GET `/api/register`
Cette route permet de créer un compte utilisateur, à partir d’un identifiant et mot de passe choisis par l’utilisateur.

Une fois le compte créé, un jeton Personal Access Token est généré et retourné pour
effectuer d’autre requêtes au nom de cet utilisateur.

#### Propriétés JSON attendues dans le corps de la requête

- `name` : nom de l'utilisateur
- `email` : adresse email unique choisie par l’utilisateur
- `password` : mot de passe choisi par l’utilisateur

#### Propriétés JSON en réponse d'une requête correcte

- `token` (type: string) : En cas de succès, cette propriété aura pour valeur le Personal
Access Token généré pour l’utilisateur, en version "plain text".

#### Cas d’erreurs

- si `name` , `email` et/ou `password` sont manquants : Retourner un code HTTP
422
- si `password` contient moins de 8 caractères : Retourner un code HTTP 422.
- si `email` est invalide : Retourner un code HTTP 422.
- si `email` est déjà associé à un utilisateur existant en base de données : Retourner
un code HTTP 422.

### Route GET `/api/login`
Cette route permet à un utilisateur de se connecter à son compte, en fournissant son
identifiant et son mot de passe.

Une fois le compte créé, un jeton Personal Access Token est généré et retourné pour
effectuer d’autre requêtes au nom de cet utilisateur.

#### Propriétés JSON attendues dans le corps de la requête

- `email` : adresse email unique choisie par l’utilisateur
- `password` : mot de passe choisi par l’utilisateur
  
#### Propriétés JSON en réponse d'une requête correcte

- `token` (type: string) : En cas de succès, cette propriété aura pour valeur le Personal Access Token généré pour l’utilisateur, en version "plain text".

#### Cas d’erreurs

- si `email` n'est associé à aucun compte existant en base de données : "Cette
adresse e-mail est inconnue" et retourner un code HTTP 422.
- si `password` ne correspond pas à l'adresse e-mail du compte : "Identifiants
incorrects" et retourner un code HTTP 422.

### Route GET `/api/notes`

Cette route permet de lister ses notes, dans l’ordre anti-chronologique de création.

Le personal access token au format `bearer` de l’utilisateur connecté doit être fourni dans le header HTTP `Authorization` .

#### Propriétés JSON en réponse d'une requête correcte

- `notes` (type: array) : en cas de succès, cette propriété aura pour valeur un tableau
d’objets respectant le schéma de la table notes. (fourni plus haut)

#### Cas d'erreurs

- Si l’utilisateur n’est pas connecté : retourner un code HTTP 401.

### Route GET `/api/notes/{id}`

Cette route permet de récupérer une note existante.

Le personal access token au format `bearer` de l’utilisateur connecté doit être fourni dans le header HTTP `Authorization`.

#### Paramètres attendus dans l’URL de la requête

- `id` : identifiant unique de la note à modifier

#### Propriétés JSON attendues dans le corps de la requête

- `content` : contenu de la note saisie par l’utilisateur.

#### Propriétés JSON en réponse d'une requête correcte

- `note` (type: object): En cas de succès, cette propriété aura pour valeur un l’objet qui a été mis à jour dans la table notes. *(cf schéma de la table notes fourni plus haut)*

#### Cas d’erreurs

- si `id` n’est associé à aucune note stockée dans la base de données : rretourner un
code HTTP 404.
- si l’utilisateur n’est pas connecté : retourner un code HTTP 401.
- si `id` est associé à une note appartenant à un autre utilisateur : retourner un code
HTTP 403.

### Route POST `/api/notes`
Cette route permet d’ajouter une note.

Le personal access token au format `bearer` de l’utilisateur connecté doit être fourni dans le header HTTP `Authorization` .

#### Propriétés JSON attendues dans le corps de la requête

- `content` : contenu de la note saisie par l’utilisateur.
  
#### Propriétés JSON en réponse d'une requête correcte

- `note` (type: object) : En cas de succès, cette propriété aura pour valeur un l’objet qui a été inséré dans la table notes, comprenant son `id` . *(cf schéma de la table notes fourni plus haut)*

#### Cas d'erreurs

- si l’utilisateur n’est pas connecté : retourner un code HTTP 401.
- si `content` est manquant : Retourner un code HTTP 422.

### Route PUT `/api/notes/{id}`

Cette route permet de modifier une note existante.

Le personal access token au format `bearer` de l’utilisateur connecté doit être fourni dans le header HTTP `Authorization` .

#### Paramètres attendus dans l’URL de la requête

- `id` : identifiant unique de la note à modifier
  
#### Propriétés JSON attendues dans le corps de la requête

- `content` : contenu de la note saisie par l’utilisateur. *(mise à jour)*
  
#### Propriétés JSON en réponse d'une requête correcte

- `note` (type: object): En cas de succès, cette propriété aura pour valeur un l’objet qui a été mis à jour dans la table notes, comprenant son `id`. *(cf schéma de la table notes fourni plus haut)*

#### Cas d’erreurs

- si l’utilisateur n’est pas connecté : retourner un code HTTP 401.
- si `content` est manquant : Retourner un code HTTP 422
- si `id` n’est associé à aucune note stockée dans la base de données : Retourner un
code HTTP 404.
- si `id` est associé à une note appartenant à un autre utilisateur : Retourner un code
HTTP 403.

### Route DELETE `/api/notes/{id}`

Cette route permet de supprimer une de ses notes.

Le personal access token au format `bearer` de l’utilisateur connecté doit être fourni dans le header HTTP `Authorization` .

#### Paramètres attendus dans l’URL de la requête

- `id` : identifiant unique de la note à supprimer.

#### Propriétés JSON en réponse d'une requête correcte

- Renvoyer `null`

#### Cas d’erreurs

- si l’utilisateur n’est pas connecté : retourner un code HTTP 401.
- si `id` n’est associé à aucune note stockée dans la base de données : Retourner un
code HTTP 404.
- si `id` est associé à une note appartenant à un autre utilisateur : Retourner un code
HTTP 403.

### Route DELETE `/api/reset`

Pour les besoins des tests HTTP, vous allez devoir créer cette route qui permet de faire un "rest" de la base de données : supprimer tous les utilisateurs, personal access tokens et notes.

> Note : Si cette route ne fonctionne pas, alors que les tests http dépendent d'une base de données réinitialisée, tous vos tests seront faux.

## Rendus

Vous devrez rendre deux URLs :
- l’URL du dépôt GitHub contenant le code source, l’historique de commits
  [URL GIT](https://github.com/MacInLife/Laravel-Sanctum-Notes-API)
- l’URL à laquelle l’API a été déployée en production
  [API Heroku déployé]()