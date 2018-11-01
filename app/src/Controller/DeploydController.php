<?php
namespace App\Controller;

class DeploydController
{
    protected $logger;
    protected $apiserver;

    public function __construct(\Psr\Log\LoggerInterface $logger, $apiserver)
    {
        $this->logger       = $logger;
        $this->apiserver    = $apiserver;
        $this->guzzle       = new \GuzzleHttp\Client();
    }

    /**
     * Post object to Deployd API
     * @param  string   $collection     collection name
     * @param  array    $data           object data
     * @return array
     */
    public function post(string $collection, array $data)
    {
        $this->logger->warning(substr(strrchr(rtrim(__CLASS__, '\\'), '\\'), 1).': '.__FUNCTION__);
        try {
            $res = $this->guzzle->request("POST", $this->apiserver."/".$collection, [
                "form_params" => $data
            ]);
            $response = json_decode($res->getBody(), true);
        } catch (ClientException $e) {
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
        
        return $response;
    }

    /**
     * Update object in Deployd API
     * @param  string   $collection     collection name
     * @param  string   $id             document id
     * @param  array    $data           object data
     * @return array
     */
    public function put(string $collection, string $id, array $data)
    {
        $this->logger->warning(substr(strrchr(rtrim(__CLASS__, '\\'), '\\'), 1).': '.__FUNCTION__);
        try {
            $res = $this->guzzle->request("PUT", $this->apiserver."/".$collection."/".$id, [
                "form_params" => $data
            ]);
            $response = json_decode($res->getBody(), true);
        } catch (ClientException $e) {
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
        
        return $response;
    }

    /**
     * Get object(s) from Deployd API
     * @param  string   $collection     collection name
     * @param  string   $id             document id
     * @param  string   $query          get/search query
     * @return array
     */
    public function get(string $collection, string $id = null, string $query = null)
    {
        $this->logger->warning(substr(strrchr(rtrim(__CLASS__, '\\'), '\\'), 1).': '.__FUNCTION__);
        if ($id == null && $query == null) {
            try {
                $res = $this->guzzle->request("GET", $this->apiserver."/".$collection);
                $response = json_decode($res->getBody(), true);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $response["_status"] = $e->getResponse()->getStatusCode();
                $response["_error"] = true;
            }
        } elseif ($query == null) {
            try {
                $res = $this->guzzle->request("GET", $this->apiserver."/".$collection."/".$id);
                $response = json_decode($res->getBody(), true);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $response["_status"] = $e->getResponse()->getStatusCode();
                $response["_error"] = true;
            }
        } else {
            try {
                $res = $this->guzzle->request("GET", $this->apiserver."/".$collection."/?".$query);
                $response = json_decode($res->getBody(), true);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $response["_status"] = $e->getResponse()->getStatusCode();
                $response["_error"] = true;
            }
        }

        return $response;
    }

    /**
     * Delete object from Deployd API
     * @param  string   $collection     collection name
     * @param  string   $id             document id
     * @return array
     */
    public function del(string $collection, string $id)
    {
        $this->logger->warning(substr(strrchr(rtrim(__CLASS__, '\\'), '\\'), 1).': '.__FUNCTION__);
        try {
            $res = $this->guzzle->request("DELETE", $this->apiserver."/".$collection."/".$id);
            $response = json_decode($res->getBody(), true);
        } catch (ClientException $e) {
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }

        return $response;
    }
}