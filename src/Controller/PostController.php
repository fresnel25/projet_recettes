<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(HttpClientInterface $client): Response
    {
        // 1. Faire une requête GET vers l'API
        $response = $client->request(
            'GET',
            'https://api.escuelajs.co/api/v1/products'
        );

        // 2. Récupérer le contenu JSON sous forme de tableau PHP
        $produits = $response->toArray();

        // 3. Afficher le résultat en dd
        /* dd($produits); */

        return $this->render('post/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/post_with_token', name: 'app_post_with_token')]
    public function indexWithToken(HttpClientInterface $client): Response
    {
        //
        $responseToken = $client->request(
            'POST',
            'https://api.escuelajs.co/api/v1/auth/login', // URL sécurisée
            [
                'json' => [
                    "email" => "john@mail.com",
                    "password" => "changeme"
                ]
            ]
        );

        $tokenData = $responseToken->toArray();
        $token = $tokenData['access_token'];

        // Vérifier le token
        // dd($token);

        // 
        $response = $client->request(
            'GET',
            'https://api.escuelajs.co/api/v1/products',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ]
            ]
        );

        $posts = $response->toArray();

        dd($posts);

        return new Response("OK");
    }
}
