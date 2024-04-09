<?php 
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LanguageController extends AbstractController
{
    #[Route('/change-language/{lang}', name: 'change_language')]
    public function changeLanguage(Request $request, $lang): Response
    {
        
        $session = $request->getSession();
        $session->set('_locale', $lang);

        return $this->redirectToRoute('app_main', ['_locale' =>
        $session->get('_locale')]
    );
    }
}