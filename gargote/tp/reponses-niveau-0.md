## 0.2 Lire le schéma relationnel

### 1-schéma relationnel des 4 tables
categorie(**id**,libele)
plat (**id**, nom, prix, #id_categorie)
commande(**id**,date_commande,numero_table)
commande_ligne(**id**,#id_commande,#id_plat,quantite,prix_unitaire)

### 2
commande_ligne contient une colonne prix_unitaire, alors que le prix est déjà dans plat car si le prix du plat change demain,le prix des anciennes commandes restent justes et le 

exemple concret où les deux valeurs seraient différentes.

le 01/09/2026 
### Table `plat`

| id | nom | prix | id_categorie |
| --- | --- | --- | --- |
|1|Ravitoto sy henakisoa|8000|1|

---
le 01/10/2026 
### Table `plat`

| id | nom | prix | id_categorie |
| --- | --- | --- | --- |
|1|Ravitoto sy henakisoa|7000|1|


### `commande`

| id | date_commande | numero_table | 
| --- | --- | --- | 
|1|02/09/2026|1|
|2|02/10/2026|1|

### Table `commande_ligne`

| id | id_commande | id_plat | quantite | prix_unitaire|
| --- | --- | --- | --- | --- |
|1|1|1|1|8000|
|1|2|1|1|7000|

---
## 3
Que se passe-t-il pour les lignes d’une commande quand on supprime la commande ?
-> les lignes d’une commande sont supprimés
 Quelle instruction en est responsable ?
-> ON DELETE CASCADE

## 4
Trois données absurdes que le schéma actuel accepte sans broncher 
->  1-la colonne prix de la table plat accepte une valeur négative
    2-on peut donner tout les numero de tables qu'on veut : on peut passer une commande a une table numéro 10000 (sans meme savoir si elle existe)
    3-losqu'on supprime ou modifie un plat, on n'a pas l'historique des valeurs des anciens plats /et commandes

## 0.3 Interroger l’API à la main
5- Pour la requête b,  code = 201 
on n'a pas obtenu 200 car c'est une création

6-Pour la requête d, la suppression est blocké par PostgreSQL 
ligne de code qui transforme ce blocage en réponse 409:
``` javascript
    return $this->erreur('Ce plat figure déjà dans des commandes, il ne peut pas être supprimé.', 409);
```

7-Creation d'une commande avec curl (table 6, deux Romazava et un Café). 

curl -i -X POST http://localhost:8080/api/commandes \
     -H "Content-Type: application/json" \
     -d '{ "numero_table": 6,
           "lignes": [
                {"id_plat": 4, "quantite":2},
                {"id_plat": 10, "quantite":1} ] }'

## 0.4 Suivre une requête de bout en bout

| # | Étape | Fichier | Fonction / ligne |
|---|---|---|---|
| 1 | Le formulaire est soumis | `components/PlatForm.vue` |enregistrer() |
| 2 | La requête HTTP est construite |api.js | `requete('POST', '/plats', plat)` |
| 3 | Vite transmet `/api/...` au port 8080 |vite.config.js | '/api': 'http://localhost:8080',|
| 4 | CodeIgniter choisit le contrôleur |app/config/routes.php | $routes->post('plats', 'Plats::create');|
| 5 | Le contrôleur lit le JSON |app/controllers/Plats.php | $donnees = $this->donneesJson(); |
| 6 | Les règles de validation sont vérifiées |app/controllers/Plats.php | return $this->erreurValidation($this->plats->errors()); |
| 7 | La ligne est insérée | app/controllers/Plats.php|return $this->respondCreated($this->plats->find($id)); |
| 8 | Le formulaire prévient l’écran parent | | `emit('enregistre', resultat)` |
| 9 | L’écran recharge la liste | `views/CarteView.vue` |charger() |

8. Le code métier de la création d’une commande se trouve dans Services/CommandeService.php
Il n’est pas dans le contrôleur `Commandes.php`  car 
 * Le contrôleur reçoit la requête HTTP et renvoie la réponse ;
 * le service décide si l'opération est permise et l'exécute.
