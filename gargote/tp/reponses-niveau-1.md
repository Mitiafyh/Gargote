## A. Base de données
### 1.1 Script de migration
1. Je choisit date_jour comme clé primaire pour que PostgreSQL interdit d'enregistrer deux lignes avec la même date.

2. En ajoutant simplement une colonne est_plat_du_jour BOOLEAN dans plat:
- on ne pourra pas avoir l'historique des plats du jour,
- on devrait verifier et changer tous les données de la table plat (false pour tous les plats sauf le plat du jour) à chaque fois qu'on change le plat du jour.

**vérification:**  La seconde exécution échoue, c'est normal mon script ajoute des colonnes sans vérifier elles existent déjà donc lors de la deuxième exécution, PostgreSQL signale qu'ils existent déjà.

### 1.2 Données de test
3. Insertion d'un plat à 0 Ar
->new row for relation "plat" violates check constraint "ck_plat_prix"
Insertion dun plat dont le nom existe déjà 
->duplicate key value violates unique constraint "uq_plat_nom"

## B. API et code métier
### 1.5 Le plat du jour
4. 422 est pour marquer des données invalides alors qu'ici les données sont valides mais il y a un conflit avec l'état actuel du plat qui est épuisé ce qui fait que R2 renvoie 409.
5. Si Bao marque comme épuisé le plat qui est déjà plat du jour  le plat se retire du plat du jour car comme on a la règle qu'un plat épuisé ne peut pas devenir plat du jour donc un plat du jour épuisé n'est plus un plat du jour.

## C. Validations
### 1.7 Jeu de tests
```javascript
curl -i -X POST http://localhost:8080/api/plats \
     -H "Content-Type: application/json" \
     -d '{"nom":"Café","prix":1000,"id_categorie":3}'
HTTP/1.1 422 Unprocessable Content
Host: localhost:8080
Date: Wed, 04 Nov 2026 13:13:27 GMT
Connection: close
X-Powered-By: PHP/8.3.36
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1793798007.245670
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1793798007.245670

{
    "message": "Données invalides.",
    "errors": {
        "nom": "Un plat portant ce nom existe déjà."
    }
}
```
```javascript
curl -i -X PUT http://localhost:8080/api/plats/4 \\
     -H "Content-Type: application/json" \
     -d '{"nom":"Romazava","prix":7500}'
HTTP/1.1 200 OK
Host: localhost:8080
Date: Wed, 04 Nov 2026 13:14:09 GMT
Connection: close
X-Powered-By: PHP/8.3.36
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1793798049.368873
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1793798049.368873

{
    "id": 4,
    "nom": "Romazava",
    "prix": 7500,
    "id_categorie": 1,
    "description": "Un délicieux romazava",
    "disponible": true
}
```
```javascript
curl -i -X POST http://localhost:8080/api/plats \
     -H "Content-Type: application/json" \
     -d '{"nom":"Thé","prix":0,"id_categorie":3}'
HTTP/1.1 422 Unprocessable Content
Host: localhost:8080
Date: Wed, 04 Nov 2026 13:16:24 GMT
Connection: close
X-Powered-By: PHP/8.3.36
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1793798184.719276
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1793798184.719276

{
    "message": "Données invalides.",
    "errors": {
        "prix": "Le prix doit être un nombre entier strictement positif."
    }
}
```
```javascript
curl -i -X POST http://localhost:8080/api/plats \
     -H "Content-Type: application/json" \
     -d '{"nom":"Thé","prix":500,"id_categorie":99}'
HTTP/1.1 422 Unprocessable Content
Host: localhost:8080
Date: Wed, 04 Nov 2026 13:16:55 GMT
Connection: close
X-Powered-By: PHP/8.3.36
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1793798215.074673
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1793798215.074673

{
    "message": "Données invalides.",
    "errors": {
        "id_categorie": "La catégorie choisie n’existe pas."
    }
}
```
```javascript
curl -i -X PUT http://localhost:8080/api/plats/7 \\
     -H "Content-Type: application/json" \
     -d '{"disponible":"oui"}'
HTTP/1.1 422 Unprocessable Content
Host: localhost:8080
Date: Wed, 04 Nov 2026 13:17:14 GMT
Connection: close
X-Powered-By: PHP/8.3.36
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1793798234.041265
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1793798234.041265

{
    "message": "Données invalides.",
    "errors": {
        "disponible": "Valeur booléenne attendue (true, false, 0, 1, \"0\" ou \"1\")."
    }
}
```
```javascript
curl -i -X PUT http://localhost:8080/api/plats/7 \\
     -H "Content-Type: application/json" \
     -d '{"disponible":false}'
HTTP/1.1 200 OK
Host: localhost:8080
Date: Wed, 04 Nov 2026 13:17:34 GMT
Connection: close
X-Powered-By: PHP/8.3.36
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1793798254.814500
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1793798254.814500

{
    "id": 7,
    "nom": "Mi sao",
    "prix": 6500,
    "id_categorie": 2,
    "description": null,
    "disponible": false
}
```
```javascript
curl -i -X PUT http://localhost:8080/api/plat-du-jour \\
     -H "Content-Type: application/json" \
     -d '{"id_plat":7}'
HTTP/1.1 409 Conflict
Host: localhost:8080
Date: Wed, 04 Nov 2026 13:18:02 GMT
Connection: close
X-Powered-By: PHP/8.3.36
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1793798282.324259
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1793798282.324259

{
    "message": "Le Mi sao est épuisé : il ne peut pas être plat du jour.",
    "errors": {}
}
```
```javascript
curl -i "http://localhost:8080/api/plats?categorie=2&disponible=1"

HTTP/1.1 200 OK
Host: localhost:8080
Date: Wed, 04 Nov 2026 13:18:24 GMT
Connection: close
X-Powered-By: PHP/8.3.36
Cache-Control: no-store, max-age=0, no-cache
Content-Type: application/json; charset=UTF-8
Debugbar-Time: 1793798304.814808
Debugbar-Link: http://localhost:8080/index.php?debugbar_time=1793798304.814808

[
    {
        "id": 6,
        "nom": "Soupe chinoise",
        "prix": 6000,
        "id_categorie": 2,
        "description": null,
        "disponible": true,
        "categorie": "Soupe"
    }
]
```
6. Avant la règle is_not_unique, la quatrième requête renvoyait 500 car postgreSQL refusait d'inserer le plat à cause de la contrainte de clé étrangère et puisque php ne sait gerer cette erreur alors le serveur renvoie 500. 
C'est postgreSQL détectait l’erreur mais c'est  un problème de laisser PostgreSQL la détecter seul car l'utilisateur ne comprends pas l'erreur alors on a besoin de la règle is_not_unique pour que php détecte l'erreur et renvoie 422 avec un message compréhensible pour l'utilisateur.