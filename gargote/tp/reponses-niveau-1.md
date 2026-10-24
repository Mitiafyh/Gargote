## A. Base de données
### 1.1 Script de migration
1. Je choisit date_jour comme clé primaire pour que PostgreSQL interdit d'enregistrer deux lignes avec la même date.

2. En ajoutant simplement une colonne est_plat_du_jour BOOLEAN dans plat:
- on ne pourra pas avoir l'historique des plats du jour,
- on devrait verifier et changer tous les données de la table plat (false pour tous les plats sauf le plat du jour) à chaque fois qu'on change le plat du jour.

**vérification:**  La seconde exécution échoue, c'est normal mon script ajoute des colonnes sans vérifier elles existent déjà donc lors de la deuxième exécution, PostgreSQL signale qu'ils existent déjà.

