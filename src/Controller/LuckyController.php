<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LuckyController extends AbstractController
{
    #[Route('/lucky/number')]
    public function number(): Response
    {
        $number = random_int(0, 100);

        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }

    #[Route('/contact')]
    public function contact(): Response
    {
        $nom = "Jerôme";
        return $this->render('contact.html.twig', [
            "nom"=>$nom
        ]);
    }

    #[Route('/home')]
    public function home(): Response
    {

        return new Response(
            '<html><body>Bienvenue sur ma page d\'accueil</body></html>'
        );
    }
}