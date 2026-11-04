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
