<?php

namespace App\Entity\Shop;

use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasGedmoTimestampTrait;
use App\Entity\Traits\HasIdNameTrait;
use App\Entity\Traits\HasIsOnlineTrait;
use App\Entity\Traits\HasMetaTrait;
use App\Repository\Shop\BrandRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
#[UniqueEntity('name')]
// #[Vich\Uploadable]
class Brand
{
    use HasIdNameTrait;
    use HasMetaTrait;
    use HasIsOnlineTrait;
    use HasGedmoTimestampTrait;
    use HasDeletedAtTrait;
}
