<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Rating;
use App\Form\RatingType;
use App\Repository\RatingRepository;

class MovieController extends AbstractController
{
    /**
     * @Route("/admin/movie", name="admin_movie_index", methods={"GET"})
     */
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('admin/movie/index.html.twig', [
            'movies' => $movieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/movie/new", name="admin_movie_new", methods={"GET","POST"})
     */
    public function new(Request $request, ObjectManager $manager): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // J'attribue des valeurs aux champs manquants (objets) dans le formulaire
            //...
            $manager->persist($movie);
            $manager->flush();

            return $this->redirectToRoute('admin_movie_index');
        }

        return $this->render('admin/movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/movie/{id}", name="admin_movie_show")
     */
    public function show(Movie $movie): Response
    {
        return $this->render('admin/movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/movie/{slug}", name="movie_show")
     */
    public function frontshow(Movie $movie, Request $request, ObjectManager $manager): Response
    {
        $rating = new Rating();
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // J'attribue des valeurs aux champs manquants (objets) dans le formulaire
            $rating->setAuthor($this->getUser())
                ->setMovie($movie)
                ->setCreatedAt(new \DateTime());
            $manager->persist($rating);
            $manager->flush();

            return $this->redirectToRoute('movie_show', ['slug' => $movie->getSlug()]);
        }
        return $this->render('front/movie/show.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/movie/{id}/edit", name="admin_movie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Movie $movie, ObjectManager $manager): Response
    {
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('admin_movie_index');
        }

        return $this->render('admin/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/movie/{id}/delete", name="admin_movie_delete")
     */
    public function delete(Movie $movie, ObjectManager $manager): Response
    {
        $manager->remove($movie);
        $manager->flush();
        return $this->redirectToRoute('admin_movie_index');
    }
}
