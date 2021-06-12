<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateQuizController extends AbstractController
{
    /**
     * @Route("/create/quiz", name="create_quiz")
     */
    public function index(): Response
    {
        return $this->render('create_quiz/index.html.twig', [
            'categories' => $this->getCategories(),
            'controller_name' => 'CreateQuizController',
        ]);
    }

    private function getCategories()
    {
        return $this->getDoctrine()->getRepository(Category:: class)->findAll();
    }
}
