<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Services\Mailer;
use Exception;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UpdateProfileController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $em;
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(ObjectManager $em,Mailer $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }
    // UPDATE User Infos

    /**
     * @Route("/informations/{id}/modification", name="update.profile.index")
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('home');
        }
        $userEmail = $this->getUser()->email;
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $requestEmail = $request->request->all('registration')['email'];
            if ($userEmail !== $requestEmail) {
                $token = $this->generateToken();
                $user->setToken($token);
                $user->setEnabled(false);
                $user->setPassword($hash);
                $this->em->flush();
                $this->addFlash("success", "Pour vous connecter, valider votre email");
                $this->mailer->sendEmail($user->getEmail(), $token, $user->getUsername());
                $this->get('security.token_storage')->setToken(null);
                return $this->redirectToRoute('app_login');
            }
            $this->em->flush();
        }
        return $this->render('update_profile/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $this->getCategories()
        ]);
    }

    private function getCategories()
    {
        return $this->getDoctrine()->getRepository(Category:: class)->findAll();
    }

    /**
     * @throws Exception
     */
    private function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
