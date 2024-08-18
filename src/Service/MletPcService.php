<?php

namespace App\Service;

use App\Entity\MletPc;
use App\Repository\MletpcRepository;


class MletPcService
{
    private MletPcRepository $mletPcRepository;

    public function __construct(MletPcRepository $mletPcRepository)
    {
        $this->mletPcRepository = $mletPcRepository;
    }

    public function saveMletPc(MletPc $mletPc): void
    {
        $this->mletPcRepository->save($mletPc);
    }

    public function deleteMletPcById(int $mletPcId): void
    {
        $mletPc = $this->getMletpcById($mletPcId);
        $this->mletPcRepository->remove($mletPc);
    }

    public function getMletpcById(int $id): ?MletPc
    {
        return $this->mletPcRepository->find($id);
    }

    public function findAllMletPcs(): array
    {
        return $this->mletPcRepository->findAll();
    }

    public function updateMletpc(MletPc $mletPc): void
    {
        $this->mletPcRepository->save($mletPc);
    }
}
