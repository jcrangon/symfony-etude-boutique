<?php

namespace App\Controller;

use App\Entity\PaymentMethod;
use App\Entity\Transporteur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\AppHelpers;
use App\Service\OrderManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use App\Service\PanierManager;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderController extends AbstractController
{
    private string $bodyId;
    private $app;
    private $db;
    private $userInfo;
    private $cartCount;
    private $session;
    private $orderManager;

    public function __construct(Security $security, ManagerRegistry $doctrine,  AppHelpers $app, PanierManager $cartManager, RequestStack $requestStack, OrderManager $orderManager)
    {
        $this->app = $app;
        $this->bodyId = $app->getBodyId('ORDER_PAGE');
        $this->db = $doctrine->getManager();
        $this->userInfo = $app->getUser();
        $this->orderManager = $orderManager;

        $this->session = $requestStack->getSession();
        if (null !== $this->userInfo->user) {
            if (null !== $this->session->get('cartCount')) {
                $this->cartCount = (int)$this->session->get('cartCount');
            } else {
                $this->session->set('cartCount', $cartManager->getCartCount($this->userInfo->user));
                $this->cartCount = (int)$this->session->get('cartCount');
            }
        }
    }

    public function index(int $step, Request $request): Response
    {
        // $this->session->remove('checkoutData');
        // $this->session->remove('step');
        // exit();
        // on sécurise
        // si le panier est vide, on redirige
        if ($this->cartCount === 0) {
            return $this->redirectToRoute('app_category');
        }
        // si l'etape n'est pas spécifiée en url
        // on redirige
        if (null === $step || $step < 1 || $step > 4) {
            return $this->redirectToRoute('app_cart');
        }
        // si l'etape autorisée n'est pas présente en session
        // on redirige
        if (null === $this->session->get('step') && $step === 1) {
            $this->session->set('step', 1);
        } else if (null === $this->session->get('step') && $step !== 1) {
            return $this->redirectToRoute('app_cart');
        }
        // si l'etape spécifiée en url est supérieur
        // à l'étape autorisée en session
        if ((int)$step > (int)$this->session->get('step')) {
            if ((int)$step === 4) {
                return $this->redirectToPayment();
            } else {
                return $this->redirectToRoute('app_order', ['step' => $this->session->get('step')]);
            }
        }

        // on verifie la mise en session des données de checkout
        $this->loadCheckoutData();
        // dd($this->session->get('checkoutData'));

        // on créé un variable qui contiendra le 
        //formulaire correspondant au step
        $stepForm = $this->processStep($step, $request);
        //dd($this->session->get('checkoutData'));

        // on sécurise si $stepForm ne contient pas un formulaire
        if (!$stepForm) {
            throw new Exception('Formulaire non défini!');
        }

        // render
        return $this->render('order/index.html.twig', [
            'bodyId' => $this->bodyId,
            'userInfo' => $this->userInfo,
            'cartCount' => $this->cartCount,
            'step' => (int)$step,
            'stepForm' => $stepForm->createView(),
            'cartTotals' => $this->session->get('checkoutData')['orderTotals']['cartTotals'],
            'totalLivraisonHT' => (float) $this->session->get('checkoutData')['orderTotals']['totalLivraisonHT'],
            'tva20' => (float) $this->session->get('checkoutData')['orderTotals']['tva20'],
            'totalTTC' => (float) $this->session->get('checkoutData')['orderTotals']['totalTTC'],
            'transporteurSelected' => $this->session->get('checkoutData')['shippingMethod'],
            'transporteurs' => $this->db->getRepository(Transporteur::class)->findAll(),
            'paymentMethods' => $this->db->getRepository(PaymentMethod::class)->findAll(),
            'paymentMethodSelected' => (int)$this->session->get('checkoutData')['payment']['method'],
        ]);
    }

    private function processStep($step, $request)
    {
        switch ($step) {
            case 1:
                // on charge la session avec ces données
                $defaultOrderAddressFormData = $this->session->get('checkoutData')['adresseLivraison'];

                $orderAddressForm = $this->getForm($step, $defaultOrderAddressFormData);

                //on charge les éléments de requête
                $orderAddressForm->handleRequest($request);

                if ($orderAddressForm->isSubmitted() && $orderAddressForm->isValid()) {
                    // data is an array with "name", "email", and "message" keys
                    $data = $orderAddressForm->getData();
                    $checkoutData = $this->session->get('checkoutData');
                    $checkoutData['adresseLivraison'] = $data;
                    $this->session->set('checkoutData', $checkoutData);
                }

                // si l'adresse est correctement remplie en session
                // on incremente le step
                $checkoutData = $this->session->get('checkoutData');
                if (
                    !empty($checkoutData['adresseLivraison']) &&
                    !empty($checkoutData['adresseLivraison']['adresse']) &&
                    !empty($checkoutData['adresseLivraison']['codePostal']) &&
                    !empty($checkoutData['adresseLivraison']['ville'])
                ) {
                    $this->session->set('step', 2);
                }

                // on transmet le formulaire au stepForm
                return $orderAddressForm;

                break;

            case 2:
                $defaultFormData = [];
                $shippingMethodForm = $this->getForm($step, $defaultFormData);

                //on charge les éléments de requête
                $shippingMethodForm->handleRequest($request);

                if ($shippingMethodForm->isSubmitted() && $shippingMethodForm->isValid()) {
                    // data is an array with "name", "email", and "message" keys
                    $data = $shippingMethodForm->getData();
                    $checkoutData = $this->session->get('checkoutData');
                    //dd($data);
                    $transporteur = $this->db->getRepository(Transporteur::class)->find($data['shipping']);
                    $checkoutData['shippingMethod']['id'] = $transporteur->getId();
                    $checkoutData['shippingMethod']['nom'] = $transporteur->getNom();
                    $checkoutData['shippingMethod']['prix'] = $transporteur->getPrix();
                    $checkoutData['shippingMethod']['description'] = $transporteur->getDescription();

                    $checkoutData['orderTotals']['totalLivraisonHT'] = $transporteur->getPrix();

                    $checkoutData['orderTotals']['tva20'] = round((($checkoutData['orderTotals']['cartTotals']->totalHT + $checkoutData['orderTotals']['totalLivraisonHT'])  * 20 / 100), 2, PHP_ROUND_HALF_UP);

                    $checkoutData['orderTotals']['totalTTC'] = $checkoutData['orderTotals']['cartTotals']->totalHT + $checkoutData['orderTotals']['totalLivraisonHT'] + $checkoutData['orderTotals']['tva20'];

                    $this->session->set('checkoutData', $checkoutData);
                }

                // on incrémene le step
                $this->session->set('step', 3);

                return $shippingMethodForm;
                break;

            case 3:
                $default = [];
                $emptyform = $this->getForm($step, $default);

                if (null !== $request->get('changePaymentMethodForm')) {
                    //dd($request);
                    $selectedPaymentId = $request->get('paymentmethod');
                    $checkoutData = $this->session->get('checkoutData');

                    $checkoutData['payment']['method'] = $selectedPaymentId;
                    $this->session->set('checkoutData', $checkoutData);
                }
                return $emptyform;
                break;
        }
    }

    private function loadCheckoutData()
    {
        if (null === $this->session->get('checkoutData')) {
            $checkoutData = [];
            $checkoutData['adresseLivraison'] = [];
            $checkoutData['orderTotals'] = [];
            $checkoutData['shippingMethod'] = [];
            $checkoutData['payment'] = [];
            $this->session->set('checkoutData', $checkoutData);
        } else {
            $checkoutData = $this->session->get('checkoutData');
        }

        // on securise l'existence des tableaux inteernes
        if (!isset($checkoutData['adresseLivraison'])) {
            $checkoutData['adresseLivraison'] = [];
        }

        if (!isset($checkoutData['orderTotals'])) {
            $checkoutData['orderTotals'] = [];
        }
        if (!isset($checkoutData['shippingMethod'])) {
            $checkoutData['shippingMethod'] = [];
        }
        if (!isset($checkoutData['payment'])) {
            $checkoutData['payment'] = [];
        }


        // on récupère l(adresse par defaut)
        if (!isset($checkoutData['adresseLivraison']['adresse']) || empty($checkoutData['adresseLivraison']['adresse'])) {
            $checkoutData['adresseLivraison']['adresse'] = $this->userInfo->user->getAdresse();
            $checkoutData['adresseLivraison']['codePostal'] = $this->userInfo->user->getCodePostal();
            $checkoutData['adresseLivraison']['ville'] = $this->userInfo->user->getVille();
        }

        // on enregistre le transporteur par defaut dans la session
        if (!isset($checkoutData['shippingMethod']['id']) || empty($checkoutData['shippingMethod']['id'])) {
            $DefaultTransporteur = $this->db->getRepository(Transporteur::class)->find(1);
            $checkoutData['shippingMethod']['id'] = $DefaultTransporteur->getId();
            $checkoutData['shippingMethod']['nom'] = $DefaultTransporteur->getNom();
            $checkoutData['shippingMethod']['prix'] = $DefaultTransporteur->getPrix();
            $checkoutData['shippingMethod']['description'] = $DefaultTransporteur->getDescription();
        }

        // on récupère les totaux du panier
        // on récupère dynamiquement car un utilisateur
        // pourrait utiliser la barre d'adresse  et ne pas
        // repasser par la page panier.
        $checkoutData['orderTotals']['cartTotals'] = $this->orderManager->getCartTotals($this->userInfo->user->getPanier(), 20);


        //on récupère les montants
        if (!isset($checkoutData['orderTotals']['totalLivraisonHT']) || empty($checkoutData['orderTotals']['totalLivraisonHT'])) {
            $checkoutData['orderTotals']['totalLivraisonHT'] = $this->orderManager->getTotalLivraison();
        }

        // on enregistre le montant de la tva
        if (!isset($checkoutData['orderTotals']['tva20']) || empty($checkoutData['orderTotals']['tva20'])) {
            $checkoutData['orderTotals']['tva20'] = round((($checkoutData['orderTotals']['cartTotals']->totalHT + $checkoutData['orderTotals']['totalLivraisonHT'])  * 20 / 100), 2, PHP_ROUND_HALF_UP);
        }

        // on enregistre le montant TTC
        $checkoutData['orderTotals']['totalTTC'] =
            $checkoutData['orderTotals']['cartTotals']->totalHT + $checkoutData['orderTotals']['totalLivraisonHT'] + $checkoutData['orderTotals']['tva20'];



        // on enregistre l'option de payment
        if (!isset($checkoutData['payment']['method']) || empty($checkoutData['payment']['method'])) {
            $pm = $this->db->getRepository(PaymentMethod::class)->findAll();

            $checkoutData['payment']['method'] = $pm[0]->getId();
        }

        $this->session->set('checkoutData', $checkoutData);
    }

    private function getForm($step, $default = [])
    {
        switch ($step) {

            case 1:
                return $this->createFormBuilder($default)
                    ->add('adresse', TextType::class, [
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Ce champs est obligatoire',
                            ]),
                            new Length([
                                'min' => 3,
                                'minMessage' => 'Ce champs doit contenir au moins 3 caractères...',
                            ]),
                        ],
                    ])
                    ->add('codePostal', TextType::class, [
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Ce champs est obligatoire',
                            ]),
                            new Length([
                                'min' => 5,
                                'max' => 5,
                                'exactMessage' => 'Ce champs doit contenir 5 caractères...',
                            ]),
                        ],
                    ])
                    ->add('ville', TextType::class, [
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Ce champs est obligatoire',
                            ]),
                            new Length(['min' => 3]),
                        ],
                    ])
                    ->add('Modifier', SubmitType::class)
                    ->getForm();
                break;

            case 2:
                $form = $this->createFormBuilder($default);

                $transporteurs = $this->db->getRepository(Transporteur::class)->findAll();

                $choices = [];

                foreach ($transporteurs as $transporteur) {
                    $choices[$transporteur->getNom()] = $transporteur->getId();
                }
                $form->add('shipping', ChoiceType::class, [
                    'choices' => $choices,
                    'choice_attr' => [
                        'class' => 'btn-check shipping-method-radio'
                    ],


                    'multiple' => false,
                    'expanded' => true,
                ]);
                return $form->getForm();
                break;

            case 3:
                $form = $this->createFormBuilder($default);
                return $form->getForm();
                break;

            default:
                throw new Exception('Formulaire inconnu !');
        }
    }

    private function redirectToPayment()
    {
        $chosenPaymentId = $this->session->get('checkoutData')['payment']['method'];
        $chosenPayment = $this->db->getRepository(PaymentMethod::class)->find($chosenPaymentId);

        // on vérifie le nom du moyen choisi.
        // Si ce dernier ne s'appelle pas Paypal
        // alors on redirige vers stripe
        if ($chosenPayment->getName() !== 'Paypal') {
            return $this->redirectToRoute('app_stripe');
        } else {
            return $this->redirectToRoute('app_paypal');
        }
    }
}
