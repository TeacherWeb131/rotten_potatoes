<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Form\RatingType;
use App\Repository\RatingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;

class RatingController extends AbstractController
{
    /**
     * @Route("/admin/rating", name="admin_rating_index")
     */
    public function index(RatingRepository $ratingRepository): Response
    {
        return $this->render('admin/rating/index.html.twig', [
            'ratings' => $ratingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/rating/new", name="admin_rating_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $rating = new Rating();
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rating);
            $entityManager->flush();

            return $this->redirectToRoute('admin_rating_index');
        }

        return $this->render('admin/rating/new.html.twig', [
            'rating' => $rating,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/rating/{id}", name="admin_rating_show")
     */
    public function show(Rating $rating): Response
    {
        return $this->render('admin/rating/show.html.twig', [
            'rating' => $rating,
        ]);
    }

    /**
     * @Route("/admin/rating/{id}/edit", name="admin_rating_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Rating $rating): Response
    {
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_rating_index');
        }

        return $this->render('admin/rating/edit.html.twig', [
            'rating' => $rating,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/rating/{id}/delete", name="admin_rating_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Rating $rating, ObjectManager $manager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rating->getId(), $request->request->get('_token'))) {
            $manager->remove($rating);
            $manager->flush();
        }

        return $this->redirectToRoute('admin_rating_index');
    }
}
