<?php

namespace App\Models;

use CodeIgniter\Model;

class PlatModel extends Model
{
    protected $table      = 'plat';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    // PostgreSQL renvoie les nombres sous forme de texte : on les convertit.
    protected array $casts = [
        'id'           => 'int',
        'prix'         => 'int',
        'id_categorie' => 'int',
        'disponible'   => 'boolean',
    ];

    // Seuls ces champs peuvent être écrits par insert() / update().
    // Un champ ajouté dans la table doit AUSSI être ajouté ici.
    protected $allowedFields = ['nom', 'prix', 'id_categorie','description','disponible'];

    // Règles de validation : vérifiées automatiquement avant insert() / update().
    // Liste des règles disponibles :
    // https://codeigniter.com/user_guide/libraries/validation.html#available-rules
    protected $validationRules = [
        'id'           => 'permit_empty|is_natural_no_zero',
        'nom'          => 'required|min_length[2]|max_length[100]|is_unique[plat.nom,id,{id}]',
        'prix'         => 'required|integer|is_natural_no_zero|less_than_equal_to[100000]',
        'id_categorie' => 'required|integer|is_not_unique[categorie.id]',
        'description'  => 'permit_empty|max_length[255]',
        'disponible'   => 'permit_empty|est_booleen',
    ];

    protected $validationMessages = [
        'nom' => [
            'required'   => 'Le nom du plat est obligatoire.',
            'min_length' => 'Le nom doit contenir au moins 2 caractères.',
            'max_length' => 'Le nom ne doit pas dépasser 100 caractères.',
            'is_unique'  => 'Un plat portant ce nom existe déjà.',
        ],
        'prix' => [
            'required' => 'Le prix est obligatoire.',
            'is_natural_no_zero' => 'Le prix doit être un nombre entier strictement positif.',
            'integer'  => 'Le prix doit être un nombre entier (en Ariary).',
            'less_than_equal_to' => 'Le prix est au plus 100 000 Ar.',
        ],
        'id_categorie' => [
            'required' => 'Choisissez une catégorie.',
            'is_not_unique' => 'La catégorie choisie n’existe pas.',
        ],
        'description' => [
            'max_length' => 'La description ne doit pas dépasser 255 caractères.',
        ],
        'disponible' => [
            'est_booleen' => 'Le champ "disponible" doit être un booléen (true, false, 0, 1, "0" ou "1").',
        ],
    ];

    /**
     * Liste des plats avec le libellé de leur catégorie.
     * Si $idCategorie est fourni, ne garde que cette catégorie.
     */
    public function listerAvecCategorie(?int $idCategorie = null, ?bool $disponible = null): array
    {
        $this->select('plat.*, categorie.libelle AS categorie')
            ->join('categorie', 'categorie.id = plat.id_categorie')
            ->orderBy('categorie.libelle')
            ->orderBy('plat.nom');

        if ($idCategorie !== null) {
            $this->where('plat.id_categorie', $idCategorie);
        }
        if ($disponible !== null) {
            $this->where('plat.disponible', (bool) $disponible);
        }

        return $this->findAll();
    }
}
