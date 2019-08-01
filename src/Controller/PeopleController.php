<?php

namespace App\Controller;

use App\Entity\People;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PeopleController extends AbstractController
{
    /**
     * @Route("/people/{id}", name="people_show")
     */
    public function show(People $people): Response
    {
        return $this->render('front/people/show.html.twig', [
            'people' => $people,
        ]);
    }
}
