<?php
namespace App\Controller;

use Psr\Log\LoggerInterface;
use \GuzzleHttp\Client;
use Cocur\Slugify\Slugify;

class ApiController
{
    protected $logger;
    protected $mcache;
    protected $fcache;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger       = $logger;
        $this->client       = new \GuzzleHttp\Client();
    }

    public function postMedia($obj)
    {
        $this->logger->info('API | -----------------------------');
        $this->logger->info('API | postMedia');
        
        $res = $this->client->request('POST', 'http://127.0.0.1:8090/media', [
            'form_params' => $obj
        ]);
        $response = json_decode($res->getBody(), true);
        
        return $response;
    }

    public function cropImage($url)
    {
        $this->logger->info('API | -----------------------------');
        $this->logger->info('API | cropImage | '.$url);

        $obj["url"] = $url;

        $res = $this->client->request('POST', 'http://127.0.0.1:8090/crop', [
            'form_params' => $obj
        ]);
        $response = json_decode($res->getBody(), true);
        
        return $response;
    }

    public function postNews($obj)
    {
        $this->logger->info('API | -----------------------------');
        $this->logger->info('API | postNews');
        
        $res = $this->client->request('POST', 'http://127.0.0.1:8090/news', [
            'form_params' => $obj
        ]);
        $response = json_decode($res->getBody(), true);
        
        return $response;
    }

    public function putNews($id, $obj)
    {
        $this->logger->info('API | -----------------------------');
        $this->logger->info('API | putNews');
        
        $res = $this->client->request('PUT', 'http://127.0.0.1:8090/news/'.$id, [
            'form_params' => $obj
        ]);
        $response = json_decode($res->getBody(), true);
        
        return $response;
    }

    public function getNews($id)
    {
        $this->logger->info('API | -----------------------------');
        $this->logger->info('API | getNews | '.$id);
        
        $res = $this->client->request('GET', 'http://127.0.0.1:8090/news', [
            'query' => ['id' => $id]
        ]);
        $response = json_decode($res->getBody(), true);
        
        return $response;
    }
}