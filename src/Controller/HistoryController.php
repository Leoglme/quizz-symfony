<?php
namespace App\Controller;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends AbstractController
{
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    /**
     * @Route("/historique", name="history.index")
     */
    public function index(): Response
    {
        $history = $this->session->get('history');
        $notEmpty = !($history === null);
        return $this->render('history/index.html.twig', [
            'categories' => $this->getCategories(),
            'histories' => $history,
            'notEmpty' => $notEmpty,
        ]);
    }

    private function getCategories()
    {
        return $this->getDoctrine()->getRepository(Category:: class)->findAll();
    }
}
