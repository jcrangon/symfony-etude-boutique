<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\UserAuthenticator;
use App\Service\AppHelpers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Service\PanierManager;
use Symfony\Component\HttpFoundation\RequestStack;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private $app;
    private string $bodyId;
    private $userInfo;
    private $cartCount;
    private $session;

    public function __construct(EmailVerifier $emailVerifier,  AppHelpers $app, PanierManager $cartManager, RequestStack $requestStack)
    {
        $this->app = $app;
        $this->bodyId = $app->getBodyId('SIGN_UP');
        $this->emailVerifier = $emailVerifier;
        $this->userInfo = $app->getUser();

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


    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Membre();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $today = new \DateTime('today');
            $user->setStatut(0);
            $user->setDateEnregistrement($today);
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('security@fake-paris.com', 'Fake Boutique Paris'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            // return $userAuthenticator->authenticateUser(
            //     $user,
            //     $authenticator,
            //     $request
            // );
            return $this->redirectToRoute('app_email_conf_sent');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'bodyId' => $this->bodyId,
            'userInfo' => $this->userInfo,
            'cartCount' => $this->cartCount,
        ]);
    }

    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_home');
    }

    public function postSignUp()
    {
        return $this->render('registration/post_sign_up.html.twig', [
            'bodyId' => $this->bodyId,
            'userInfo' => $this->userInfo,
            'cartCount' => $this->cartCount,
        ]);
    }
}
