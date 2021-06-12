<?php
namespace App\Controller;
use App\Entity\Category;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Controller\ShowReponseController;
use App\Form\LoginType;
use App\Form\QuestionType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ShowQuizController extends AbstractController
{


    /**
     * @var ObjectManager
     */
    private $em;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     *
     * @param ObjectManager $em
     * @param SessionInterface $session
     */

    public function __construct(ObjectManager $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    /**
     * @Route("/category/{id}/{name}/{count}/{number}", name="show_quiz_harry", requirements={"id"="\d+"})
     * @param $id
     * @param $number
     * @param $name
     * @param $count
     * @param \App\Controller\ShowReponseController $reponse
     * @param Request $request
     * @param Reponse $response
     * @return Response
     */

    public function showQuiz($id, $number, $name, $count, ShowReponseController $reponse, Request $request, Reponse $response): Response
    {
        $firstIdQuestion = (intval($id) - 1) * 10 + 1;
        $idQuestion = intval($number) - $firstIdQuestion + 1;
        if ($idQuestion === 1) {
            $_SESSION['responseValid'] = 0;
        }
        $categories = $this->getCategories();
        $question = $this->getDoctrine()
            ->getRepository(Question:: class)
            ->findOneBy(array("idCategorie" => $id, "id" => $number));

        $tab = $reponse->showReponses($number, $id);

        $defaultData = [];
        foreach ($tab as $item) {
            $defaultData["response$item->id"] = $item->reponse;
        }
        $form = $this->createFormBuilder($defaultData)
            ->add("response", ChoiceType::class, [
                'choices' => [
                    $defaultData["response" . $tab[0]->id] => $tab[0]->id,
                    $defaultData["response" . $tab[1]->id] => $tab[1]->id,
                    $defaultData["response" . $tab[2]->id] => $tab[2]->id
                ],
                'expanded' => true,
                'label' => false,
                'required' => true,
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $requestChoice = $request->request->all('form');
            $responseExpected = $this->getDoctrine()->getRepository(Reponse::class)
                ->findOneBy(array('reponseExpected' => true, 'idQuestion' => $number));
            $isResponseValid = $responseExpected->id == $requestChoice['response'];
            if ($isResponseValid) {
                $_SESSION['responseValid'] += 1;
            }
            if ($idQuestion < intval($count)) {
                return $this->redirectToRoute('show_quiz_harry',
                    array('id' => $id,
                        'name' => $name,
                        'count' => $count,
                        'number' => strval($number + 1)
                    ));
            }else{
                /*Historique quiz user*/
                $history = [
                    'categories' => [
                        'name' => $name,
                        'id' => $id
                    ],
                    'result' => [
                        'goodResponse' => $_SESSION['responseValid'],
                        'totalQuestion' => $count
                    ]
                ];
                $sessionVal = $this->get('session')->get('history');
                $sessionVal[] = $history;
                $this->get('session')->set('history', $sessionVal);


                return $this->render('quizz/quiz-result.html.twig', [
                    'category' => $name,
                    'categories' => $categories,
                    'countQuestion' => $count,
                    'countValideResponse' => $_SESSION['responseValid']
                ]);
            }
        }

        return $this->render('quizz/quiz-index.html.twig', [
            'question' => $question->getQuestion(),
            'responses' => $tab,
            'categories' => $categories,
            'form' => $form->createView(),
            'currentPage' => $number - $firstIdQuestion + 1,
            'totalPage' => $count,
            'correct' => $idQuestion === 1 ? 0 : $_SESSION['responseValid'],
            'incorrect' => $idQuestion === 1 ? 0 : $idQuestion - $_SESSION['responseValid'] - 1
        ]);

    }

    private function getCategories()
    {
        return $this->getDoctrine()->getRepository(Category:: class)->findAll();
    }

}
