<?php
namespace App\Service;

use App\Repository\SortiesRepository;
use App\Entity\Sorties;

class SortieService
{
    private SortiesRepository $sortiesRepository;

    public function __construct(SortiesRepository $sortiesRepository)
    {
        $this->sortiesRepository = $sortiesRepository;
    }


    public function getSortieDetails(int $id): ?Sorties
    {
        return $this->sortiesRepository->find($id);
    }
}