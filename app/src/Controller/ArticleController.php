<?php
namespace App\Controller;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Cocur\Slugify\Slugify;
use Slim\Http\Request;
use Slim\Http\Response;

final class ArticleController
{
    private $view;
    private $logger;

    public function __construct(Twig $view, LoggerInterface $logger, $media, $api)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->media = $media;
        $this->api = $api;
        $this->slugify = new \Cocur\Slugify\Slugify();
    }

    public function viewArticle($request, $response, $args)
    {
        $request  = new \Slim\Http\MobileRequest($request);
        $response = new \Slim\Http\MobileResponse($response);

        if ($request->isMobile()) {
            $isMobile = true;
        } else {
            $isMobile = false;
        }

        $id = $args['id'];
        $language = $args['language'];

        $article = $this->api->getNews($id);

        $isVideo = false;

        $this->logger->info("Article VIEW action dispatched");

        $data = ['isMobile' => $isMobile, 'language' => $language, 'isVideo' => $isVideo, 'article' => $article];

        $this->view->render($response, 'article.twig', $data);
        return $response;
    }

    public function addArticle($request, $response, $args)
    {
        $request  = new \Slim\Http\MobileRequest($request);
        $response = new \Slim\Http\MobileResponse($response);

        if ($request->isMobile()) {
            $isMobile = true;
        } else {
            $isMobile = false;
        }

        $language = $args['language'];

        if ($request->isGet()) {
            $this->logger->info("Article ADD GET action dispatched");

            $data = ['isMobile' => $isMobile, 'language' => $language];
            $this->view->render($response, 'article-add.twig', $data);
            return $response;
        } else {
            $this->logger->info("Article ADD POST action dispatched");
            $allPostVars = $request->getParsedBody();
            
            $article["headline"] = $allPostVars['headline'];
            $article["descriptionHtml"] = $allPostVars['descriptionHtml'];
            $article["articleBody"] = $allPostVars['articleBody'];
            $article["articleSection"] = $allPostVars['articleSection'];
            $article["inLanguage"] = $language;
            $article["dateCreated"] = date('c');
            $article["version"] = 1;

            if ($allPostVars["saveArticle"] == "publishArticle") {
                $article["status"] = "public";
            } elseif ($allPostVars["saveArticle"] == "scheduleArticle") {
                $article["status"] = "scheduled";
            } else {
                $article["status"] = "draft";
            }

            if (!empty($allPostVars["featuredImageOriginalUrl"])) {
                $renditions = $this->api->cropImage($allPostVars["featuredImageOriginalUrl"]);
                $article["associatedMedia"]["featuredImage"]["renditions"] = $renditions;
                $article["associatedMedia"]["featuredImage"]["original"]["href"] = $allPostVars["featuredImageOriginalUrl"];
                $article["thumbnailUrl"] = $renditions["square-small"]["href"];
            }

            if (!empty($allPostVars["featuredVideoOriginalUrl"])) {
                $article["associatedMedia"]["featuredVideo"]["href"] = $allPostVars["featuredVideoOriginalUrl"];
            }            

            //sa nu uiti sa schimb in slug line shi la add shi redit tot
            $article["slug"] = $this->slugify->slugify($allPostVars["headline"]);

            $post = $this->api->postNews($article);

            if (!empty($allPostVars["featuredImageOriginalUrl"])) {
                $featuredImage["name"] = basename($allPostVars["featuredImageOriginalUrl"]);
                $featuredImage["headline"] = basename($allPostVars["featuredImageOriginalUrl"]);
                $featuredImage["inLanguage"] = $language;
                $featuredImage["dateCreated"] = date('c');
                $featuredImage["version"] = 1;
                $featuredImage["contentUrl"] = $allPostVars["featuredImageOriginalUrl"];
                $featuredImage["thumbnailUrl"] = $renditions["square-small"]["href"];
                $featuredImage["associatedMedia"]["renditions"] = $renditions;
                $featuredImage["descriptionText"] = $allPostVars["featured_image_descriptionText"];
                $featuredImage["copyrightHolder"]["type"] = "link";
                $featuredImage["copyrightHolder"]["id"] = "";
                $featuredImage["copyrightHolder"]["name"] = $allPostVars["featured_image_copyrightHolder"];
                $associateditem = (object) array("type" => "news", "id" => $post["id"]);
                $featuredImage["associatedItems"][] = $associateditem;
                $featuredImage["type"] = "image";
                $featuredImage["width"] = $allPostVars["featuredImageOriginalWidth"];
                $featuredImage["height"] = $allPostVars["featuredImageOriginalHeight"];
                $featuredImage["encodingFormat"] = $allPostVars["featuredImageOriginalMime"];

                $media = $this->api->postMedia($featuredImage);

                unset($associateditem);
            }

            if (!empty($allPostVars["featuredVideoOriginalUrl"])) {
                $featuredVideo["inLanguage"] = $language;
                $featuredVideo["dateCreated"] = date('c');
                $featuredVideo["version"] = 1;
                $featuredVideo["contentUrl"] = $allPostVars["featuredVideoOriginalUrl"];
                $featuredVideo["descriptionText"] = $allPostVars["featured_video_descriptionText"];
                $associateditem = (object) array("type" => "news", "id" => $post["id"]);
                $featuredVideo["associatedItems"][] = $associateditem;
                $featuredVideo["encodingFormat"] = $allPostVars["featuredVideoOriginalMime"];
                if (strpos($allPostVars["featuredVideoOriginalUrl"], 'youtu') > 0) {
                    // youtube
                    $youtube_json = file_get_contents('http://www.youtube.com/oembed?url='. $allPostVars["featuredVideoOriginalUrl"] . '&format=json');
                    $youtube_info = json_decode($youtube_json, true);
                    $featuredVideo["name"] = $youtube_info['title'];
                    $featuredVideo["headline"] = $youtube_info['title'];
                    $featuredImage["width"] = $youtube_info['width'];
                    $featuredImage["height"] = $youtube_info['height'];
                    $featuredVideo["type"] = "youtube";
                    $featuredVideo["copyrightHolder"]["type"] = "link";
                    $featuredVideo["copyrightHolder"]["id"] = "";
                    $featuredVideo["copyrightHolder"]["name"] = $youtube_info['author_name'].' / YouTube';
                } else {
                    // mp4
                    $featuredVideo["name"] = basename($allPostVars["featuredVideoOriginalUrl"]);
                    $featuredVideo["headline"] = basename($allPostVars["featuredVideoOriginalUrl"]);
                    $featuredVideo["width"] = $allPostVars["featuredVideoOriginalWidth"];
                    $featuredVideo["height"] = $allPostVars["featuredVideoOriginalHeight"];
                    $featuredVideo["type"] = "video";
                    $featuredVideo["copyrightHolder"]["type"] = "link";
                    $featuredVideo["copyrightHolder"]["id"] = "";
                    $featuredVideo["copyrightHolder"]["name"] = $allPostVars["featured_video_copyrightHolder"];
                }

                $media = $this->api->postMedia($featuredVideo);

                unset($associateditem);
            }

            return $response->withRedirect("/".$language."/edit/news/".$post["id"]);
        }
    }

    public function editArticle($request, $response, $args)
    {
        $request  = new \Slim\Http\MobileRequest($request);
        $response = new \Slim\Http\MobileResponse($response);

        if ($request->isMobile()) {
            $isMobile = true;
        } else {
            $isMobile = false;
        }

        $id = $args['id'];
        $language = $args['language'];

        $article = $this->api->getNews($id);
        
        $data = ['isMobile' => $isMobile, 'language' => $language, 'article' => $article];
        $this->view->render($response, 'article-edit.twig', $data);
        return $response;
    }
}
