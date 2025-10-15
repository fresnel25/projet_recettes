<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends AbstractController
{
    #[Route('/lucky', name: 'app_lucky')]
    public function index(): Response
    {
        return $this->render('lucky/index.html.twig', [
            'controller_name' => 'LuckyController',
        ]);
    }

    #[Route('/contact', name: 'contact.index')]
    public function show_number(): Response
    {
        $number = random_int(0, 100);
        return new Response('Nombre tiré au sort : ' . $number);
    }

    #[Route('/lucky/number_for_username', name: 'number_for_username.show_number_v2')]
    public function show_number_v2(Request $request): Response
    {
        // On récupère le paramètre "username" depuis l'URL
        $username = $request->query->get('username', 'inconnu');

        // On tire un nombre aléatoire
        $number = random_int(0, 100);

        return new Response(
            'Méthode 2 → Nombre tiré au sort : ' . $number . ' pour ' . $username
        );
    }

    #[Route('/lucky/number_v3', name: 'number_v3.show_number_v3')]
    public function show_number_v3 (Request $req ) : Response {
        $number = random_int(1, 100);
        return $this->render('lucky/number.html.twig',["number" => $number]);

    }
}
