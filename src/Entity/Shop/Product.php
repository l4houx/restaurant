<?php

namespace App\Entity\Shop;

use App\Entity\HomepageHeroSetting;
use App\Entity\Review;
use App\Entity\Traits\HasContentTrait;
use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasIdNameSlugTrait;
use App\Entity\Traits\HasIsOnlineTrait;
use App\Entity\Traits\HasLimit;
use App\Entity\Traits\HasMetaTrait;
use App\Entity\Traits\HasSocialNetworksTrait;
use App\Entity\Traits\HasTagTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Entity\Traits\HasViewsTrait;
use App\Entity\User;
use App\Repository\Shop\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[UniqueEntity('name')]
#[UniqueEntity('slug')]
#[Vich\Uploadable]
class Product
{
    use HasIdNameSlugTrait;
    use HasContentTrait;
    use HasMetaTrait;
    use HasIsOnlineTrait;
    use HasViewsTrait;
    use HasTagTrait;
    use HasSocialNetworksTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;

    public const PRODUCT_LIMIT = HasLimit::PRODUCT_LIMIT;

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

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $imageMimeType = null;

    #[ORM\Column(type: Types::TEXT, length: 1000, nullable: true)]
    private ?string $imageOriginalName = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private ?array $imageDimensions = [];

    #[ORM\Column(type: Types::STRING, length: 20, unique: true)]
    private string $ref = '';

    #[ORM\Column(type: Types::INTEGER)]
    private int $amount;

    #[ORM\Column(type: Types::FLOAT, precision: 5, scale: 4)]
    #[Assert\GreaterThan(0)]
    private float $tax;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\PositiveOrZero(message: 'Stock cannot be negative')]
    private ?int $stock = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Assert\NotNull]
    private bool $isFeaturedProduct = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Assert\NotNull]
    private bool $isNewArrival = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    #[Assert\NotNull(groups: ['create', 'update'])]
    private bool $enablereviews = true;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Brand $brand;

    /**
     * @var Collection<int, ProductImage>
     */
    #[ORM\OneToMany(targetEntity: ProductImage::class, mappedBy: 'product', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $images;

    #[ORM\ManyToOne(inversedBy: 'products', cascade: ['persist'])]
    private ?HomepageHeroSetting $isonhomepageslider = null;

    /**
     * @var collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'favorites', cascade: ['persist', 'remove'])]
    #[ORM\JoinTable(name: 'favorites')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private Collection $addedtofavoritesby;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'product', orphanRemoval: true, cascade: ['remove'])]
    private Collection $reviews;

    public function stringifyStatus(): string
    {
        if (!$this->isOnline) {
            return 'Offline';
        } elseif (!$this->isOnline) {
            return 'Draft';
        } elseif (!$this->isOnline) {
            return 'Schedule';
        } else {
            return 'Online';
        }
    }

    public function stringifyStatusClass(): string
    {
        if (!$this->isOnline) {
            return 'danger';
        } elseif (!$this->isOnline) {
            return 'warning';
        } elseif (!$this->isOnline) {
            return 'info';
        } else {
            return 'success';
        }
    }

    public function __toString(): string
    {
        return sprintf('#%d %s', $this->getId(), $this->getName());
    }

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->addedtofavoritesby = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function hasContactAndSocialMedia(): bool
    {
        return $this->externallink
            || $this->phone || $this->email
            || $this->youtubeurl || $this->twitterurl
            || $this->instagramurl || $this->facebookurl
            || $this->googleplusurl || $this->linkedinurl
        ;
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

    public function getImagePlaceholder(string $size = 'default'): string
    {
        if ('small' == $size) {
            return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEYAAABGCAMAAABG8BK2AAAA/1BMVEUAAAD2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhFzESFhAAAAVHRSTlMAAQIDBQYICQsNDhAREhQVGBobISQmLC0wMTQ1ODlAQkNLT1xdY2ZpbXR1d3mDhoiLjpGSlZeYnaCjpaaqtLq8yMrMzs/R1d7k5ujp6+3z9ff5+/1JGcHEAAABMUlEQVRYw+3W2U4CQRCF4dMzAqO47zvuu6iAorjgDjpuQL3/s3jjBTCkulLpRBP7f4AvmaqeTgM+n8/3Gw2un5R/Km6NKRGzRx2VAhWTp66uNMoUJcopmLMk86BgXpMMKabTQ6EoaM9oma7i3YwLhuhjwglDXwNOGDp3w1Dohsm6YYb/PVOci0Rnn2Oa2ykV0clU+9VIG1MyANC3tHNaZjqa4ZlLAyB9aJ91xDF3IYCFhl2JDcO8ZACsSBY/yXxUYwQ9L/dkq9yIpwFkPwXKPrepNQCpmkCpcAs/AGBuBMp9yDAXAFAQKDFzs9NtAGBToDRHmVNcTwNYlCxpnvsZhgCMtwTKBixFbwLl2KaEjwLl2noN5QVKxf46sA/mfdmKALlnjmg9FWZ1zzifz+f7c30DdESL++xXlRkAAAAASUVORK5CYII=';
        } else {
            return 'data:image/png;base64,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';
        }
    }

    public function getRef(): string
    {
        return $this->ref;
    }

    public function setRef(string $ref): static
    {
        $this->ref = $ref;

        return $this;
    }

    public function getFormattedAmount(): string
    {
        // return number_format(($this->amount / 100), 2, '.', ',');
        return number_format($this->amount / 100, 0, '', ' ');
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getTax(): ?float
    {
        return $this->tax;
    }

    public function setTax(float $tax): static
    {
        $this->tax = $tax;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function isFeaturedProduct(): bool
    {
        return $this->isFeaturedProduct;
    }

    public function getIsFeaturedProduct(): bool
    {
        return $this->isFeaturedProduct;
    }

    public function setIsFeaturedProduct(bool $isFeaturedProduct): static
    {
        $this->isFeaturedProduct = $isFeaturedProduct;

        return $this;
    }

    public function isNewArrival(): bool
    {
        return $this->isNewArrival;
    }

    public function getIsNewArrival(): bool
    {
        return $this->isNewArrival;
    }

    public function setIsNewArrival(bool $isNewArrival): static
    {
        $this->isNewArrival = $isNewArrival;

        return $this;
    }

    public function isEnablereviews(): bool
    {
        return $this->enablereviews;
    }

    public function getEnablereviews(): bool
    {
        return $this->enablereviews;
    }

    public function setEnablereviews(bool $enablereviews): static
    {
        $this->enablereviews = $enablereviews;

        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getBrand(): Brand
    {
        return $this->brand;
    }

    public function setBrand(Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection<int, ProductImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ProductImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(ProductImage $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    public function getIsonhomepageslider(): ?HomepageHeroSetting
    {
        return $this->isonhomepageslider;
    }

    public function setIsonhomepageslider(?HomepageHeroSetting $isonhomepageslider): static
    {
        $this->isonhomepageslider = $isonhomepageslider;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAddedtofavoritesby(): Collection
    {
        return $this->addedtofavoritesby;
    }

    public function addAddedtofavoritesby(User $addedtofavoritesby): static
    {
        if (!$this->addedtofavoritesby->contains($addedtofavoritesby)) {
            $this->addedtofavoritesby->add($addedtofavoritesby);
        }

        return $this;
    }

    public function removeAddedtofavoritesby(User $addedtofavoritesby): static
    {
        $this->addedtofavoritesby->removeElement($addedtofavoritesby);

        return $this;
    }

    public function isAddedToFavoritesBy(User $user): bool
    {
        return $this->addedtofavoritesby->contains($user);
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setProduct($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
            }
        }

        return $this;
    }

    public function isRatedBy(User $user)
    {
        /** @var Review $review */
        foreach ($this->reviews as $review) {
            if ($review->getAuthor() === $user) {
                return $review;
            }
        }

        return false;
    }

    public function getRatingsPercentageForRating($rating): int|float
    {
        if (!$this->countVisibleReviews()) {
            return 0;
        }

        return round(($this->getRatingsCountForRating($rating) / $this->countVisibleReviews()) * 100, 1);
    }

    public function getRatingsCountForRating($rating): int
    {
        if (!$this->countVisibleReviews()) {
            return 0;
        }

        $ratingCount = 0;

        /** @var Review $review */
        foreach ($this->reviews as $review) {
            if ($review->getIsVisible() && $review->getRating() === $rating) {
                ++$ratingCount;
            }
        }

        return $ratingCount;
    }

    public function getRatingAvg(): int|float
    {
        if (!$this->countVisibleReviews()) {
            return 0;
        }

        $ratingAvg = 0;

        /** @var Review $review */
        foreach ($this->reviews as $review) {
            if ($review->getIsVisible()) {
                $ratingAvg += $review->getRating();
            }
        }

        return round($ratingAvg / $this->countVisibleReviews(), 1);
    }

    public function getRatingPercentage(): int|float
    {
        if (!$this->countVisibleReviews()) {
            return 0;
        }

        $ratingPercentage = 0;

        /** @var Review $review */
        foreach ($this->reviews as $review) {
            if ($review->getIsVisible()) {
                $ratingPercentage += $review->getRatingPercentage();
            }
        }

        return round($ratingPercentage / $this->countVisibleReviews(), 1);
    }

    public function countVisibleReviews(): int
    {
        $count = 0;

        /** @var Review $review */
        foreach ($this->reviews as $review) {
            if ($review->getIsVisible()) {
                ++$count;
            }
        }

        return $count;
    }
}
