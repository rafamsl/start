<?php

namespace App\Controller;

use Psr\Cache\CacheItemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Cache\CacheInterface;
use function Symfony\Component\String\u;
use Knp\Bundle\TimeBundle\DateTimeFormatter;


class VinylController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(): Response
    {
        $tracks = [
            ['song' => 'Gangsta\'s Paradise', 'artist' => 'Coolio'],
            ['song' => 'Waterfalls', 'artist' => 'TLC'],
            ['song' => 'Creep', 'artist' => 'Radiohead'],
            ['song' => 'Kiss from a Rose', 'artist' => 'Seal'],
            ['song' => 'On Bended Knee', 'artist' => 'Boyz II Men'],
            ['song' => 'Fantasy', 'artist' => 'Mariah Carey'],
        ];

        return $this->render('vinyl/homepage.html.twig', [
            'title' => 'PB & Jams',
            'tracks' => $tracks,
        ]);
    }

    #[Route('/browse/{slug}', name: 'app_browse')]
    public function browse(HttpClientInterface $httpClient,\Symfony\Contracts\Cache\CacheInterface $cache, DateTimeFormatter $timeFormatter, string $slug = null): Response
        $genre = $slug ? u(str_replace('-', ' ', $slug))->title(true) : null;
        $mixes = $cache->get('mixes_data',function (CacheItemInterface $cacheItem) use($httpClient){
            $cacheItem->expiresAfter(1000);
            $response = $httpClient->request('GET', 'https://raw.githubusercontent.com/SymfonyCasts/vinyl-mixes/main/mixes.json');
            return $response->toArray();
        });

        return $this->render('vinyl/browse.html.twig', [
            'genre' => $genre,
            'mixes' => $mixes,
        ]);
    }
}
