<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Shop\Brand;
use App\Entity\Shop\Product;
use App\Entity\Shop\Category;
use App\Entity\Shop\SubCategory;
use App\Entity\Shop\ProductImage;
use App\Entity\HomepageHeroSetting;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppShopFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    /**
     * @var array<int, SubCategory>
     */
    private array $subCategories = [];

    private int $categoryId = 0;

    /**
     * @var array<int, Category>
     */
    private array $categories = [];

    /**
     * @var array<int, Brand>
     */
    private array $brands = [];

    /**
     * @var array<int, ProductImage>
     */
    private array $productimages = [];

    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    private function createSubCategories(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; ++$i) {
            $subCategory = (new SubCategory());
            $subCategory
                ->setId($i)
                ->setName($this->faker()->unique()->sentence(1, true))
                ->setSlug($this->slugger->slug($subCategory->getName())->lower())
                ->setColor($this->faker()->hexColor())
            ;
            $this->subCategories[$i] = $subCategory;
            $manager->persist($subCategory);
        }
    }

    private function createCategories(ObjectManager $manager, Category $parent): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $category = (new Category());
            $category
                ->setId($this->categoryId)
                ->setName($this->faker()->unique()->sentence(1, true))
                ->setSlug($this->slugger->slug($category->getName())->lower())
                ->setParent($category)
                ->setNumberOfProducts(10)
            ;
            $this->categories[] = $category;
            $manager->persist($category);

            ++$this->categoryId;

            for ($l = 1; $l <= 5; ++$l) {
                $sub = (new Category());
                $sub
                    ->setId($this->categoryId)
                    ->setName($this->faker()->unique()->sentence(1, true))
                    ->setSlug($this->slugger->slug($sub->getName())->lower())
                    ->setParent($category)
                    ->setNumberOfProducts(10)
                ;
                $this->categories[] = $sub;
                $manager->persist($sub);

                ++$this->categoryId;
            }
        }
    }

    private function createBrands(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 400; ++$i) {
            /** @var Brand $brand */
            $brand = (new Brand())
                ->setId($i)
                ->setName(sprintf('Brand %d', $i))
                ->setMetaTitle(sprintf('Brand %d', $i))
                ->setMetaDescription(sprintf('Brand %d', $i))
                ->setIsOnline(true)
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;
            $this->brands[$i] = $brand;
            $manager->persist($brand);
        }
    }

    private function createHomepageHeroSetting(ObjectManager $manager): void
    {
        // Hero Setting
        $homepages = [
            1 => [
                'title' => 'Discover Product',
                'paragraph' => 'Uncover the best products',
                'content' => 'custom',
                'custom_background_name' => 'hero-section.png',
                'custom_block_one_name' => 'hero-block-1.svg',
                'custom_block_two_name' => 'hero-block-2.svg',
                'custom_block_three_name' => 'hero-block-3.svg',
                'show_search_box' => 1,
            ],
        ];

        foreach ($homepages as $key => $value) {
            $homepage = (new HomepageHeroSetting())
                ->setTitle($value['title'])
                ->setParagraph($value['paragraph'])
                ->setContent($value['content'])
                ->setCustomBackgroundName($value['custom_background_name'])
                ->setCustomBlockOneName($value['custom_block_one_name'])
                ->setCustomBlockTwoName($value['custom_block_two_name'])
                ->setCustomBlockThreeName($value['custom_block_three_name'])
                ->setShowSearchBox((bool) $value['show_search_box'])
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $manager->persist($homepage);
        }
    }

    private function createProductImages(ObjectManager $manager): void
    {
        /** @var array<int, Product> $products */
        $products = $manager->getRepository(Product::class)->findAll();

        for ($i = 1; $i <= 2000; ++$i) {
            /** @var ProductImage $productimage */
            $productimage = (new ProductImage())
                ->setId($i)
                ->setProduct($this->faker()->randomElement($products))
                ->setImageName('664e7be695487075513142.jpg')
                ->setImageSize(70649)
                ->setImageMimeType('image/jpeg')
                ->setImageOriginalName('avatar-1.jpg')
                ->setImageDimensions([256, 256])
                ->setPosition(rand(1, 2000))
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;
            $this->productimages[$i] = $productimage;
            $manager->persist($productimage);
        }
    }

    /**
     * @param EntityManagerInterface $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->createSubCategories($manager);

        $manager->persist($category = (new Category())->setId(10000)->setName($this->faker()->unique()->name));

        for ($j = 1; $j <= 20; ++$j) {
            $categoryLevel1 = (new Category());
            $categoryLevel1
                ->setId($this->categoryId)
                ->setName($this->faker()->unique()->sentence(1, true))
                ->setSlug($this->slugger->slug($categoryLevel1->getName())->lower())
                ->setParent($category)
                ->setNumberOfProducts(10)
            ;
            $this->categories[] = $categoryLevel1;
            $manager->persist($categoryLevel1);

            ++$this->categoryId;

            shuffle($this->subCategories);

            /** @var SubCategory $subCategorie */
            foreach (array_slice($this->subCategories, 0, 5) as $subCategorie) {
                $subCategorie->getCategories()->add($categoryLevel1);
            }

            $this->createCategories($manager, $categoryLevel1);

            $manager->flush();
        }

        $this->createBrands($manager);
        $manager->flush();

        /** @var array<int, HomepageHeroSetting> $homepages */
        $homepages = $manager->getRepository(HomepageHeroSetting::class)->findAll();

        /** @var array<int, User> $users */
        $users = $manager->getRepository(User::class)->findAll();

        // Create 2000 Products by User and admin
        for ($p = 1; $p <= 2000; ++$p) {
            $product = (new Product());
            $product
                ->setId($p)
                ->setName($this->faker()->unique()->sentence(5, true))
                ->setSlug($this->slugger->slug($product->getName())->lower())
                ->setContent($this->faker()->paragraphs(10, true))
                ->setCategory($this->categories[$p % count($this->categories)])
                ->setBrand($this->brands[($p % count($this->brands)) + 1])
                ->setRef(sprintf('REF_%d', $p))
                ->setAmount(intval(ceil(rand(10, 2000) / 5) * 5))
                ->setTax(0.2)
                ->setStock(rand(10, 2000))
                ->setTags($this->faker()->word())
                ->setViews(rand(10, 2000))
                ->setMetaTitle($product->getName())
                ->setMetaDescription($product->getName())
                ->setExternallink(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setWebsite(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setEmail(1 === mt_rand(0, 1) ? $this->faker()->email() : null)
                ->setPhone(1 === mt_rand(0, 1) ? $this->faker()->phoneNumber() : null)
                ->setYoutubeurl(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setTwitterUrl(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setInstagramUrl(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setFacebookUrl(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setGoogleplusUrl(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setLinkedinUrl(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setIsOnline($this->faker()->numberBetween(0, 1))
                ->setIsFeaturedProduct($this->faker()->numberBetween(0, 1))
                ->setIsNewArrival($this->faker()->numberBetween(0, 1))
                ->setEnablereviews($this->faker()->numberBetween(0, 1))
                ->addAddedtofavoritesby($this->faker()->randomElement($users))
                ->setIsonhomepageslider($this->faker()->randomElement($homepages))
                ->setIsOnline($this->faker()->numberBetween(0, 1))
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            if (2000 === $p) {
                $product->setAmount(2000);
            }

            $manager->persist($product);

            $this->createProductImages($manager);

            if ($p > 2000 - count($this->categories)) {
                $this->categories[$p % count($this->categories)]->setLastProduct($product);
            }

            if (0 === $p % 400) {
                $manager->flush();
            }
        }
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            AppSettingsFixtures::class,
        ];
    }
}
