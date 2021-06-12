<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Question;
use App\Entity\Reponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowReponseController extends AbstractController
{

    public function showReponses($id, $count)
    {
            $response = $this->getDoctrine()
                ->getRepository(Reponse:: class)
                ->findBy(["idQuestion" => $id], ["id" => "ASC"]);


            return $response;

    }
}
