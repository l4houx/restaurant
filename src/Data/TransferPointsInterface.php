<?php

namespace App\Data;

use App\Entity\Data\Transfer;

interface TransferPointsInterface
{
    public function execute(Transfer $transfer): void;
}
