<?php

namespace App\Services;

use App\Models\PlatDuJourModel;
use App\Models\PlatModel;
use RuntimeException;


class PlatDuJourService
{
    private PlatDuJourModel $plat_du_jour;
    private PlatModel $plat;

    public function __construct()
    {
        $this->plat_du_jour = model(PlatDuJourModel::class);
        $this->plat = model(PlatModel::class);
    }

    public function getPlatDuJour(): ?array
    {
        return $this->plat_du_jour->getPlatDuJour();
    }

    public function setPlatDuJour(int $idPlat): array
    {
        $plat = $this->plat->find($idPlat);
        if (!$plat) {
            throw new RuntimeException("Ce plat n'existe pas.", 422);
        }
        if (!$plat['disponible']) {
            throw new RuntimeException("Le {$plat['nom']} est épuisé : il ne peut pas être plat du jour.", 409);
        }

        $this->plat_du_jour->definirOuRemplacer($idPlat);

        return $this->plat_du_jour->getPlatDuJour();
    }

    public function getHistorique(): array
    {
        return $this->plat_du_jour->getHistorique();
    }

}
