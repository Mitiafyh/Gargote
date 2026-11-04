<?php

namespace App\Controllers\Api;

use App\Services\PlatDuJourService;
use PhpParser\Node\Stmt\TryCatch;
use RuntimeException;

class PlatDuJour extends ApiController
{
    private PlatDuJourService $plat_du_jour;

    public function __construct()
    {
        $this->plat_du_jour = new PlatDuJourService();
    }

    // GET /api/plat-du-jour
    public function index()
    {
        $result = $this->plat_du_jour->getPlatDuJour();
        return $this->respond($result);
    }

    // PUT	/api/plat-du-jour
    public function update()
    {
        $donnees = $this->donneesJson();
        $idPlat = $donnees['id_plat'] ?? null;

        try {
            if (!$idPlat) {
                throw new RuntimeException("L'identifiant du plat (id_plat) est obligatoire.", 422);
            }

            $result = $this->plat_du_jour->setPlatDuJour((int)$idPlat);
            return $this->respond($result);
        } catch (RuntimeException $e) {
            return $this->erreur($e->getMessage(), $e->getCode());
        }
    }

    // GET /api/plat-du-jour/historique
    public function historique()
    {
        $result = $this->plat_du_jour->getHistorique();
        return $this->respond($result);
    }
}
