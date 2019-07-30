<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;
use Cocur\Slugify\Slugify;
use App\Entity\Category;
use App\Entity\Movie;
use App\Entity\People;
use App\Entity\User;
use App\Entity\Rating;

class AppFixtures extends Fixture
{
    /**
     * On aura besoin de l'encoder pour encoder les passwords des utilisateurs (User)
     * 
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * On se fait injecter l'encodeur
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // use the factory to create a Faker\Generator instance
        $faker = Factory::create('fr_FR');
        $slugify = new Slugify();

        // LES CATEGORIES
        // Pour créer des fixtures en tenant compte de la relation ManyToMany (Category / Movie)
        // Je crée un tableau en dehors de la boucle de création de categories
        $categories = [];
        $categoriesTitles = ['Aventure', 'Action', 'Animation', 'Science Fiction'];
        // Creation de Categories
        foreach ($categoriesTitles as $title) {
            // Je crée un objet Category
            $category = new Category();
            // J'affecte des valeurs à cet objet Category
            $category->setTitle($title)
                ->setSlug($slugify->slugify($category->getTitle()));
            $manager->persist($category);
            // J'insère chaque category créée dans le tableau $categories
            $categories[] = $category;
        }

        // LES PEOPLE (Actors ET Directors)
        $people = [];
        for ($p = 0; $p < 70; $p++)
        {
            // Je cré un objet People (1 person)
            $person = new People();
            // J'affecte des valeurs à cet objet
            $person->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setBirthday($faker->dateTimeBetween("-70 years", "-15 years"))
                ->setPicture("http://placehold.it/150x150")
                ->setDescription($faker->realText())
                ->setSlug($slugify->slugify($person->getFirstname() . ' ' . $person->getLastname()));
            // J'ajoute la personne au tableau des people pour m'en resservir après
            $people[] = $person;

            $manager->persist($person);
        }

        // LES USER
        $users = [];
        for ($u = 0; $u < 20; $u++) {
            $user = new User;
            $user->setAvatar("http://placehold.it/150x150")
                ->setEmail("user$u@gmail.com")
                ->setPassword($this->encoder->encodePassword($user, "pass"))
                ->setName($faker->userName);
            $manager->persist($user);
            // J'ajoute l'utilisateur au tableau pour m'en resservir après
            $users[] = $user;
        }



        // LES MOVIES
        // Je crée une boucle pour répéter la création de movies (10 fois)
        for ($m = 0; $m < 50; $m++) {
            // Je crée un objet Movie
            $movie = new Movie();
            // J'affecte des valeurs à cet objet Movie
            $movie->setTitle($faker->catchPhrase)
                ->setSlug($slugify->slugify($movie->getTitle()))
                ->setPoster("http://placehold.it/400x200")
                ->setSynopsis($faker->catchPhrase)
                ->setReleasedAt($faker->dateTimeBetween('-30 years'));

            // Je lie les Movies aux Categories de manière aléatoire (Relation Movie manyToOne Category)
            // grace à une fonctionde Faker randomElements($bottedefoin, $aigille)
            // $randomCategories est un tableau de x categories aléatoires (de 1 à 3) 3 étant le max de boucle category
            $randomCategories = $faker->randomElements($categories, mt_rand(1, 3));
            //  Je lie chaque Movie aux categories aléatoires ($randomCategories)
            foreach ($randomCategories as $category) {
                $movie->addCategory($category);
            }

            // Je lie les Movies à un seul People Director de manière aléatoire (Relation Movie manyToOne People)
            $director = $faker->randomElement($people);
            $movie->setDirector($director);

            // Je lie les Movies à plusieurs Peoples Actors de manière aléatoire (Relation Movie ManyToMany People)
            $actors = $faker->randomElements($people, mt_rand(3, 8));
            foreach ($actors as $actor) {
                $movie->addActor($actor);
            }

            //LES RATING (NOTES SUR LES FILMS)
            for ($r = 0; $r < 6; $r++) {
                $rating = new Rating();
                $rating->setComment($faker->realText())
                    ->setCreatedAt($faker->dateTimeBetween("-6 months"))
                    ->setNotation(mt_rand(1, 5))
                    ->setMovie($movie)
                    // Je prend un utilisateur au hasard qui aura posté ce commentaire
                    ->setAuthor($faker->randomElement($users));
                $manager->persist($rating);
            }
            $manager->persist($movie);
        }
        $manager->flush();
    }
}
