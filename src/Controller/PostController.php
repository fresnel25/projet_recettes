<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(HttpClientInterface $client, CacheInterface $cache): Response
    {
        // ----------------------------
        // 1. Cache du traitement long
        // ----------------------------
        $texte = $cache->get('mon_texte_cache', function (ItemInterface $item) {
            $item->expiresAfter(20); // expire dans 20 secondes
            return $this->simuler_traitement_long();
        });

        // ----------------------------
        // 2. Cache des posts API
        // ----------------------------
        $produits = $cache->get('posts_api_cache', function (ItemInterface $item) use ($client) {
            $item->expiresAfter(20); // expire dans 20 secondes

            // Appel API (lent la première fois)
            $response = $client->request(
                'GET',
                'https://api.escuelajs.co/api/v1/products'
            );

            return $response->toArray();
        });

        return $this->render('post/index.html.twig', [
            'produits' => $produits,
            'texte'    => $texte
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

    public function simuler_traitement_long()
    {
        sleep(4);
        $texte = "c’était long !!";
        return $texte;
    }
}
