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

## 0.5 Repérer les notions de Vue


| Notion | À quoi ça sert  | Fichier | ligne | explication
|---|---|---|---|---|
| `ref()` | une variable dont le changement met l’écran à jour |views/CarteView.vue | const plats = ref([])|lorsqu'il y a un changement par rapport aux données des plats (suppression/ajout), la valeurs de plats est automatiquement mis à jour(la liste aussi)|
| `computed()` | une valeur calculée à partir d’autres, recalculée automatiquement |components/Panier.vue | const total = computed(() => props.lignes.reduce((somme, l) => somme + l.plat.prix * l.quantite, 0))|Calcule automatiquement le montant total du panier en additionnant le prix total de chaque plat |
| `v-for` | répéter un élément pour chaque élément d’une liste |components/Panier.vue|<li v-for="ligne in lignes":key="ligne.plat.id" .class="panier-ligne"> | lister tous les lignes (plats) du panier |
| `v-if` / `v-else` | afficher un élément seulement si une condition est vraie |components/Panier.vue |<p v-if="lignes.length === 0" class="vide">Touchez un plat de la carte pour l’ajouter.</p><ul. v-else class="panier-liste">... </ul> |Si le panier est vide, affiche un message. Sinon, affiche la liste des plats.|
| `v-model` | lier un champ de formulaire à une variable, dans les deux sens | views/CommandeView.vue | <input. v-model.number="numeroTable" type="number" min="1" /> | Lorsque l'utilisateur saisit un numero, numeroTable est mis à jour|
| `defineProps` | recevoir des données du composant parent | components/CommandeDetail.vue| defineProps({commande: { type: Object, required: true },})| recoir la valeur d'une commande venant d'une liste de commandes |
| `defineEmits` / `emit` | envoyer un événement au composant parent |components/PlatList.vue |const emit = defineEmits(['modifier', 'supprimer'])| Déclare les événements modifier et supprimer|
| `onMounted` | exécuter du code quand le composant apparaît | views/commandeView.vue|onMounted(async () => { plats.value = await api.plats.lister() })|appele la fonction lister() lorsque l'écran est chargé afin de récupérer les plats|

9. `Panier.vue` ne modifie jamais la quantité lui-même  pour separer l'affichage de la gestion de ses données. C'est le composant parent qui modifie réellement la quantité.

10. Dans `PlatForm.vue`, le `watch` surveille les changements de la valeur de props.plat. 

Sans lui quand on clique sur **Modifier** d’un autre plat ,il ne met à jour les champs du formulaire ce qui fait que c'est encore l'ancien plat qui sera  affiché

## 0.6 Premières modification

La catégorie apparaît dans la liste déroulante du formulaire sans que vous ayez écrit de code. Expliquez pourquoi, en citant la route et le composant concernés.

11.La catégorie apparaît dans la liste déroulante du formulaire sans que vous ayez écrit de code car le composant `PlatForm.vue` utilise la route `/api/categories` pour récupérer la liste des catégories.  `Categories.php`, qui renvoie les données des catégories et le composant utilise ensuite ces données pour remplir la liste déroulante automatiquement.