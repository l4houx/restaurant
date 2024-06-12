<?php

namespace App\Entity\Shop;

use App\Entity\Traits\HasIdTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Repository\Shop\ProductImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProductImageRepository::class)]
#[Vich\Uploadable]
class ProductImage
{
    use HasIdTrait;
    use HasTimestampableTrait;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Product $product = null;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'product_image', fileNameProperty: 'imageName', size: 'imageSize', mimeType: 'imageMimeType', originalName: 'imageOriginalName', dimensions: 'imageDimensions')]
    #[Assert\File(
        maxSize: '5M',
        mimeTypes: ['image/jpeg', 'image/jpg', 'image/gif', 'image/png'],
        mimeTypesMessage: 'The file should be an image'
    )]
    #[Assert\NotNull(groups: ['create'])]
    private ?File $imageFile = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['unsigned' => true])]
    private ?int $imageSize = 0;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $imageMimeType = null;

    #[ORM\Column(type: Types::TEXT, length: 1000, nullable: true)]
    private ?string $imageOriginalName = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private ?array $imageDimensions = [];

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $position = null;

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     */
    public function setImageFile(File|UploadedFile|null $imageFile): static
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->setUpdatedAt(new \DateTimeImmutable());
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(?int $imageSize): static
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getImageMimeType(): ?string
    {
        return $this->imageMimeType;
    }

    public function setImageMimeType(?string $imageMimeType): static
    {
        $this->imageMimeType = $imageMimeType;

        return $this;
    }

    public function getImageOriginalName(): ?string
    {
        return $this->imageOriginalName;
    }

    public function setImageOriginalName(?string $imageOriginalName): static
    {
        $this->imageOriginalName = $imageOriginalName;

        return $this;
    }

    public function getImageDimensions(): array
    {
        return $this->imageDimensions;
    }

    public function setImageDimensions(?array $imageDimensions): static
    {
        $this->imageDimensions = $imageDimensions;

        return $this;
    }

    public function getImagePath(): string
    {
        return 'uploads/product/'.$this->imageName;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;

        return $this;
    }
}
