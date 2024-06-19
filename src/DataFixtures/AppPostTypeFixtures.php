<?php

namespace App\DataFixtures;

use App\Entity\PostType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppPostTypeFixtures extends Fixture
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
        // Create of 4 Post Type
        $this->createPostType('Image', true, $manager);
        $this->createPostType('Video', true, $manager);
        $this->createPostType('File', true, $manager);
        $this->createPostType('Link', true, $manager);

        $manager->flush();
    }

    public function createPostType(
        string $name,
        bool $isOnline,
        ObjectManager $manager
    ) {
        $type = (new PostType());
        $type
            ->setName($name)
            ->setSlug($this->slugger->slug($type->getName())->lower())
            ->setIsOnline($isOnline)
            ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
        ;
        $manager->persist($type);

        $this->addReference('type-'.$this->autoIncrement, $type);
        ++$this->autoIncrement;

        return $type;
    }
}
