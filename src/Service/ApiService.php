<?php


namespace App\Service;

use Exception;
use Psr\Log\LoggerInterface;
use App\Service\ImageValidator;
use Symfony\Contracts\Cache\CacheInterface;

class ApiService
{

    private $cache;
    private $logger;
    private $imageValidator;

    public function __construct(CacheInterface $cache, LoggerInterface $logger, ImageValidator $imageValidator)
    {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->imageValidator = $imageValidator;
    }
    

    public function getNewsApiData($url)
    {   
        try {
            $content = file_get_contents($url);
            $newsApiData = json_decode($content);
        } catch (Exception $e) {
            $newsApiData = null;
        }
       
        return $newsApiData;
    }

    public function getCachedNewsApiData($url)
    {
        // $this->cache->delete('news_api_data'); // Forcer la mise à jour du cache pour le débogage
       
        return $this->cache->get('news_api_data', function () use ($url) {
            $data = $this->getNewsApiData($url);
            $this->logger->info('NewsApiData:', ['data' => $data]);
    
            return $data;
        });
    }
    

    public function getNewsApiItemsWithImages($newsApiData)
    {
        $newsApiItemsWithImages = array();

        if ($newsApiData !== null && isset($newsApiData->articles)) {
            foreach ($newsApiData->articles as $article) {
                if ($this->imageValidator->isValidImageUrl($article->urlToImage)) {
                    $newsApiItemsWithImages[] = $article->urlToImage;
                }
            }
        }

        return $newsApiItemsWithImages;
    }


}
