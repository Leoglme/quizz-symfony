<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationType;

use App\Repository\UserRepository;
use App\Services\Mailer;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityLoginController extends AbstractController
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var FormErrorIterator
     */
    private $formError;

    public function __construct(Mailer $mailer, UserRepository $userRepository){
        $this->mailer = $mailer;
    }

    /**
     * @throws Exception
     */
    private function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * @Route("/inscription", name="security_registration")
     * @throws Exception
     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $token = $this->generateToken();
            $user->setToken($token);
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash("success", "Pour vous connecter, valider votre email");
            $this->mailer->sendEmail($user->getEmail(), $token, $user->getUsername());
            return $this->redirectToRoute("app_login");
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
            'categories' => $this->getCategories(),
        ]);
    }

    /**
     * @Route("/confirmer-mon-compte/{token}", name="confirm_account")
     * @param string $token
     * @param ObjectManager $manager
     * @return RedirectResponse
     */
    public function confirmAccount(string $token, ObjectManager $manager): RedirectResponse
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->findOneBy(["token" => $token]);

        if($user){
            $user->setToken(null);
            $user->setEnabled(true);
            $manager->persist($user);
            $manager->flush();

        }else{
            $this->addFlash("error", "Ce compte n'existe pas");
        }
        return $this->redirectToRoute("app_login");
    }

    /**
     * @Route("/connexion", name="app_login")
     */
    public function login(Request $request, ObjectManager $manager, AuthenticationUtils $authenticationUtils): Response
    {
        $totobidule = 'doijhrefijrepjiof';
        $user = new User();
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $form = $this->createForm(LoginType::class, $user, [
            'action' => $this->generateUrl('loginGestion')]);
        $this->formError = $form;
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->flush();
            return $this->redirectToRoute("category.list");
        }

        return $this->render('security/login.html.twig', [
            'forma' => $form->createView(),
            'categories' => $this->getCategories(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/deconnexion", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    /**
     * @Route("/loginGestion", name="loginGestion")
     */
    public function loginGestion(AuthenticationUtils $authenticationUtils, UserRepository $user, Request $request): RedirectResponse
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if($error !== null){
            $this->addFlash("error", "Adresse email ou mot de passe invalide !");
            return $this->redirectToRoute("app_login");
        }
        return $this->redirectToRoute("home");
    }

    private function getCategories()
    {
        return $this->getDoctrine()->getRepository(Category:: class)->findAll();
    }

}
