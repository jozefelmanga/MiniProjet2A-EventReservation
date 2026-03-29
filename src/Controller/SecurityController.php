<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_events');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void {}

    #[Route('/register', name: 'app_register')]
public function register(
    Request $request,
    UserPasswordHasherInterface $passwordHasher,
    EntityManagerInterface $entityManager
): Response {
    $error = null;
    $lastUsername = null;

    if ($request->isMethod('POST')) {
        $lastUsername = $request->request->get('username', '');
        $submittedToken = $request->request->get('_csrf_token');

        if (!$this->isCsrfTokenValid('register', $submittedToken)) {
            $error = 'Invalid session, please try again.';
        } elseif ($lastUsername === '' || $request->request->get('password') === '') {
            $error = 'Please provide a username and password.';
        } elseif ($entityManager->getRepository(User::class)->findOneBy(['username' => $lastUsername])) {
            $error = 'Username is already taken.';
        } else {
            $user = new User();
            $user->setUsername($lastUsername);
            $user->setPassword(
                $passwordHasher->hashPassword($user, $request->request->get('password'))
            );
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }
    }

    return $this->render('security/register.html.twig', [
        'error' => $error,
        'last_username' => $lastUsername,
    ]);
}
}