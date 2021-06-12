<?php
namespace App\Controller\Admin;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminHomeController extends AbstractController
{

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var UserInterface|null
     */
    private $current_user;

    public function __construct(CategoryRepository $userRepository, UserInterface $current_user = null)
    {
        $this->categoryRepository = $userRepository;
        $this->current_user = $current_user;
    }


    // READ CATEGORY

    /**
     * @Route("/admin", name="admin.home", methods={"GET","HEAD"})
     * @return Response
     */
    public function index(): Response
    {
        if(!$this->VerifyStatus()){
            return $this->redirectToRoute('home');
        }
        $categories = $this->getCategories();
        return $this->render('admin/index.html.twig', compact(
            'categories'
        ));
    }

    //Méthode Private pour simplifier le code
    private function getCategories(): array
    {
        return $this->categoryRepository->findAll();
    }

    private function VerifyStatus(){
        $roles = $this->getStatus();
        return $roles === 'ROLE_ADMIN';
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
}
