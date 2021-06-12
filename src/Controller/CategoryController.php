<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryController extends AbstractController
{

    /**
     * @var ObjectManager
     */
    private $em;
    /**
     * @var UserInterface
     */
    private $current_user;

    public function __construct(ObjectManager $em, UserInterface $current_user = null)
    {
        $this->em = $em;
        $this->current_user = $current_user;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        //get categories data
        $userId = $this->getUser() === null ? false : $this->getUser()->id;
        $connected = (bool)$userId;
        $categories = $this->getCategories();
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
            'userId' => $userId,
            'roles' => $this->VerifyStatus(),
            'connected' => $connected,
        ]);
    }

    /**
     * @Route("/category", name="category.list")
     */
    public function categories(): Response
    {
        //get categories data
        $categories = $this->getCategories();
        return $this->render('category/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/category/{id}/{name}", name="category.index", methods={"GET","HEAD"})
     */
    public function category(int $id, string $name): Response
    {

        $categories = $this->getCategories();
        //get category data
        $category = $this->getDoctrine()->getRepository(Category:: class)->find($id);
        $number = (intval($id) - 1) * 10 + 1;

        return $this->render('category/category_count.html.twig', [
            'categories' => $categories,
            'category' => $category,
            'name' => $name,
            'number' => $number,
        ]);
    }

    /**
     * @Route("/category/{id}/{name}/{count}", name="quiz.count", methods={"GET","HEAD"})
     */
    public function showQuiz(int $id, string $name, string $count): Response
    {
        $categories = $this->getCategories();
        //get category data
        $category = $this->getDoctrine()->getRepository(Category:: class)->find($id);
        return $this->render('quizz/quiz-index.html.twig', [
            'categories' => $categories,
            'category' => $category,
            'name' => $name,
        ]);
    }

    private function getCategories()
    {
        return $this->getDoctrine()->getRepository(Category:: class)->findAll();
    }

    private function getStatus()
    {
        $userId = $this->getUser() === null ? false : $this->getUser()->id;
        if ($userId === null) {
            return false;
        }
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if(!isset($user->status)){
            return false;
        }
        return $user->status;
    }

    private function VerifyStatus(){
        $roles = $this->getStatus();
        return $roles === 'ROLE_ADMIN';
    }
}
