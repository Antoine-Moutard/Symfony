<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/sign-in', name: 'app_sign_in')]
    public function app_sign_in(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
     
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
     
        
        return $this->render('auth/sign-in.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);

        return $this->redirectToRoute('app_display_all_song');
    }
     
    #[Route('/sign-out', name: 'app_sign_out')]
    public function app_sign_out(): Response
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
        return $this->render('song/songs.html.twig',array('allSongs'=>$songs, 'message'=>$message));
    }
}
