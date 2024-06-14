<?php

namespace App\DataFixtures;

use App\Entity\HomepageHeroSetting;
use App\Entity\Shop\Brand;
use App\Entity\Shop\Category;
use App\Entity\Shop\Product;
use App\Entity\Shop\SubCategory;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppShopFixtures extends Fixture
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

    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    private function createSubCategories(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; ++$i) {
            /** @var SubCategory $subCategory */
            $subCategory = (new SubCategory())
                ->setId($i)
                ->setName(sprintf('SubCategoies %d', $i))
                ->setColor($this->faker()->hexColor())
            ;
            $this->subCategories[$i] = $subCategory;
            $manager->persist($subCategory);
        }
    }

    private function createCategories(ObjectManager $manager, Category $parent): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $manager->persist($category = (new Category())
                ->setId($this->categoryId)
                ->setParent($parent)
                ->setNumberOfProducts(10)
                ->setName(sprintf('Categorie %d', $this->categoryId)))
            ;

            ++$this->categoryId;

            for ($l = 1; $l <= 5; ++$l) {
                $manager->persist($subCategory = (new Category())
                    ->setId($this->categoryId)
                    ->setParent($category)
                    ->setNumberOfProducts(10)
                    ->setName(sprintf('Categorie %d', $this->categoryId)))
                ;

                $this->categories[] = $subCategory;
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
            ;
            $this->brands[$i] = $brand;
            $manager->persist($brand);
        }
    }

    public function load(ObjectManager $manager): void
    {
        $this->createSubCategories($manager);

        $manager->persist($category = (new Category())->setId(10000)->setName('Categorie 1'));

        for ($j = 1; $j <= 20; ++$j) {
            $manager->persist($categoryLevel1 = (new Category())
                ->setId($this->categoryId)
                ->setParent($category)
                ->setNumberOfProducts(10)
                ->setName(sprintf('Categorie %d', $this->categoryId)))
            ;

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

        /** @var array<HomepageHeroSetting> $homepages */
        $homepages = $manager->getRepository(HomepageHeroSetting::class)->findAll();

        /** @var array<int, User> $users */
        $users = $manager->getRepository(User::class)->findAll();

        $i = 1;

        // Create 2000 Products by User and admin
        for ($p = 1; $p <= 2000; ++$p) {
            /** @var string $content */
            $content = $this->faker()->sentences(5, true);

            $product = (new Product());
            $product
                ->setId($p)
                ->setName(sprintf('Product %d', $p))
                ->setContent($content)
                ->setCategory($this->categories[$p % count($this->categories)])
                ->setBrand($this->brands[($p % count($this->brands)) + 1])
                ->setRef(sprintf('REF_%d', $p))
                ->setAmount(intval(ceil(rand(10, 2000) / 5) * 5))
                ->setStock(rand(10, 2000))
                ->setTags($this->faker()->word())
                ->setViews(rand(10, 2000))
                ->setMetaTitle($product->getName())
                ->setMetaDescription($this->faker()->realText(100))
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
            ;

            $this->setReference('p-'.$p, $product);

            ++$i;

            if (2000 === $p) {
                $product->setAmount(2000);
            }

            $manager->persist($product);

            if ($p > 2000 - count($this->categories)) {
                $this->categories[$p % count($this->categories)]->setLastProduct($product);
            }

            if (0 === $p % 400) {
                $manager->flush();
            }
        }
    }
}