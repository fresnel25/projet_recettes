<?php

namespace App\Controller;

use App\Taxe\CalculatorTaxe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    #[Route('/hello', name: 'hello')]
    public function index(CalculatorTaxe $calcul): Response
    {
        $resultTVA = $calcul->calculerTVA(360);
        $resultTTC = $calcul->calculerTTC(360);
        return $this->render('hello/index.html.twig', [
            'resultTVA' => $resultTVA,
            'resultTTC' => $resultTTC
        ]);
    }



    #[Route('/hello_word', name: 'app_hello')]
    public function show_hello_word(): Response
    {
        return $this->render('hello/hello_word.html.twig');
    }
}
