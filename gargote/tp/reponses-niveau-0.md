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

