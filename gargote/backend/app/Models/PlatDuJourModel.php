<?php

namespace App\Models;

use CodeIgniter\Model;

class PlatDuJourModel extends Model
{
    protected $table      = 'plat_du_jour';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected array $casts = [
        'id'      => 'int',
        'id_plat' => 'int',
    ];

    protected $allowedFields = ['date_jour','id_plat'];

    public function getPlatDuJour(?String $date = null): ?array
    {
        $date = $date ?? date('Y-m-d');
        $result = $this->select('plat_du_jour.*, plat.nom, plat.prix, plat.description, plat.disponible, plat.id_categorie')
        ->join('plat','plat.id = plat_du_jour.id_plat')
        ->where('plat_du_jour.date_jour',$date)
        ->first();
        
        return $result ?: null;
    }

    public function definirOuRemplacer(int $idPlat): void
    {
        $sql = "INSERT INTO plat_du_jour (date_jour, id_plat) VALUES (CURRENT_DATE, ?)
                ON CONFLICT (date_jour) DO UPDATE SET id_plat = EXCLUDED.id_plat";
        $this->db->query($sql,[$idPlat]);
    }

    public function getHistorique(): array
    {
        return $this->select('plat_du_jour.*, plat.nom, plat.prix, plat.description, plat.disponible, plat.id_categorie')
        ->join('plat','plat.id = plat_du_jour.id_plat')
        ->orderBy('date_jour','DESC')
        ->limit(7)
        ->findAll();
    }

}