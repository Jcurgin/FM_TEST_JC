<?php

namespace App\Service;


use Symfony\Contracts\Cache\CacheInterface;
use App\Service\ImageValidator;

class RssService
{

    private $cache;
    private $imageValidator;

    public function __construct(CacheInterface $cache, ImageValidator $imageValidator)
    {
        $this->cache = $cache;
        $this->imageValidator = $imageValidator;
    }

    public function getRssFeed($url)
    {
        $rssItemsWithImages = [];

        try {
            $rssFeed = simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);
            $channel = $rssFeed->channel;
            $items = $channel->item;
       
            foreach ($items as $item) {
                $imageUrl = $this->recupereImageDansPage((string)$item->link);
             
                if ($imageUrl) {
                    $rssItemsWithImages[] = [
                        'url' => (string)$item->link,
                        'imageUrl' => $imageUrl
                    ];
                }
            }
        } catch (\Exception $e) {
            $rssItemsWithImages = null;
        }

        return $rssItemsWithImages;
    }

    public function getCachedRssFeed($url)
    {
        // $this->cache->delete('rss_feed'); // Forcer la mise à jour du cache pour le débogage

        return $this->cache->get('rss_feed', function () use ($url) {
            return $this->getRssFeed($url);
        }, 300);
        
    }
    
    public function getRssItemsWithImages($rssItems)
    {
        $itemsWithImages = [];
    
        if (is_array($rssItems)) {
            foreach ($rssItems as $id => $item) {
                if (!empty($item['imageUrl'])) {
                    if ($this->imageValidator->isValidImageUrl($item['imageUrl']) && $this->imageValidator->containsImage($item['imageUrl'])) {
                        $itemsWithImages[$id] = $item['imageUrl'];
                    }
                }
            }
        }
    
        return $itemsWithImages;
    }
    
    

    public function recupereImageDansPage($url)
    {
        try {
            $doc = new \DOMDocument();
            @$doc->loadHTMLFile($url);
            $xpath = new \DOMXPath($doc);

            if (strstr($url, "commitstrip.com")) {
                $query = '//img[contains(@class,"size-full")]/@src';
            } else {
                $query = '//img/@src';
            }

            $srcNodes = $xpath->query($query);
            $src = $srcNodes->length > 0 ? $srcNodes[0]->value : null;

        } catch (\Exception $e) {
            $src = null;
        }

        return $src;
    }
}
