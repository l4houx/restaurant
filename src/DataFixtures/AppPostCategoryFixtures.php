<?php

namespace App\DataFixtures;

use App\Entity\PostCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppPostCategoryFixtures extends Fixture
{
    use FakerTrait;

    private int $autoIncrement;

    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
        $this->autoIncrement = 1;
    }

    public function load(ObjectManager $manager): void
    {
        $categories = [
            [
                'name' => 'France',
                'color' => '#3f7fca',
                'isOnline' => true,
            ],
            [
                'name' => 'Politics',
                'color' => '#1e81b0',
                'isOnline' => true,
            ],
            [
                'name' => 'World',
                'color' => '#9141ac',
                'isOnline' => true,
            ],
            [
                'name' => 'Computer Science',
                'color' => '#eb230d',
                'isOnline' => true,
            ],
            [
                'name' => 'Economy',
                'color' => '#063970',
                'isOnline' => true,
            ],
            [
                'name' => 'Associations',
                'color' => '#e07b39',
                'isOnline' => true,
            ],
        ];

        foreach ($categories as $category) {
            $newcategory = new PostCategory();
            $newcategory->setName($category['name']);
            $newcategory->setSlug($this->slugger->slug($newcategory->getName())->lower());
            $newcategory->setColor($category['color']);
            $newcategory->setPostsCount(10);
            $newcategory->setIsOnline($category['isOnline']);
            $newcategory->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')));
            $newcategory->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')));

            $this->addReference('category-'.$this->autoIncrement, $newcategory);
            ++$this->autoIncrement;

            $manager->persist($newcategory);
        }

        $manager->flush();
    }
}
