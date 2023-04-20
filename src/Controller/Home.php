<?php

namespace App\Controller;

use App\Service\RssService;
use App\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class Home extends AbstractController
{
    private $rssService;
    private $apiService;
    private $sources;

    public function __construct(RssService $rssService, apiService $apiService, ParameterBagInterface $params)
    {
        $this->rssService = $rssService;
        $this->apiService = $apiService;
        $this->sources = $params->get('sources');
    }

    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $itemsWithImages = [];

          // Récupérer les données des flux RSS
          foreach ($this->sources['rss'] as $source) {
            $rssFeed = $this->rssService->getCachedRssFeed($source['url']);
            $rssItemsWithImages = $this->rssService->getRssItemsWithImages($rssFeed);
            $itemsWithImages = array_merge($itemsWithImages, $rssItemsWithImages);
        }

         // Récupérer les données des API News
         foreach ($this->sources['news_api'] as $source) {
            $newsApiData = $this->apiService->getCachedNewsApiData($source['url']);
            $newsApiItemsWithImages = $this->apiService->getNewsApiItemsWithImages($newsApiData);
            $itemsWithImages = array_merge($itemsWithImages, $newsApiItemsWithImages);
        }
       
           // Supprime les doublons
           $uniqueItems = array_unique($itemsWithImages, SORT_REGULAR);

           // Récupère les images pour chaque URL unique
           $images = [];
           foreach ($uniqueItems as $url) {
               try {
                   $images[] = $url;
               } catch (\Exception $e) {
                   // Erreur
               }
           }

        return $this->render('default/index.html.twig', ['images' => $images]);
    }    
}

