<?php
namespace App\Controller;

final class NewsController
{
    private $view;
    private $logger;
    private $deployd;

    public function __construct(\Slim\Views\Twig $view, \Psr\Log\LoggerInterface $logger, $deployd)
    {
        $this->view     = $view;
        $this->logger   = $logger;
        $this->deployd  = $deployd;
        $this->slugify  = new \Cocur\Slugify\Slugify();
    }

    public function viewNews(\Slim\Http\Request $request, \Slim\Http\Response $response, $args)
    {
        $request  = new \Slim\Http\MobileRequest($request);
        $response = new \Slim\Http\MobileResponse($response);

        if ($request->isMobile()) {
            $isMobile = true;
        } else {
            $isMobile = false;
        }

        $id         = $args['id'];
        $language   = $args['language'];

        $article    = $this->deployd->get("news", $id);

        $data       = ['isMobile' => $isMobile, 'language' => $language, 'article' => $article];
        $this->view->render($response, 'article.twig', $data);
        return $response;
    }

    public function editNews(\Slim\Http\Request $request, \Slim\Http\Response $response, $args)
    {
        $request  = new \Slim\Http\MobileRequest($request);
        $response = new \Slim\Http\MobileResponse($response);

        if ($request->isMobile()) {
            $isMobile = true;
        } else {
            $isMobile = false;
        }

        $id         = $args['id'];
        $language   = $args['language'];

        $article    = $this->deployd->get("news", $id);
        
        $data       = ['isMobile' => $isMobile, 'language' => $language, 'article' => $article];
        $this->view->render($response, 'article-edit.twig', $data);
        return $response;
    }

    public function addNews(\Slim\Http\Request $request, \Slim\Http\Response $response, $args)
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
            // get request
            $data = ['isMobile' => $isMobile, 'language' => $language];
            $this->view->render($response, 'article-add.twig', $data);
            return $response;
        } elseif ($request->isPost()) {
            // post request
            $allPostVars = $request->getParsedBody();
            
            $article["headline"]        = $allPostVars['headline'];
            $article["name"]            = $allPostVars['headline'];
            $article["description"]     = $allPostVars['descriptionHtml'];
            $article["articleBody"]     = $allPostVars['articleBody'];
            $article["articleSection"]  = $allPostVars['articleSection'];
            $article["inLanguage"]      = $language;
            $article["dateCreated"]     = date('c');
            $article["version"]         = 1;

            if ($allPostVars["saveArticle"] == "publishArticle") {
                $article["status"] = "public";
            } elseif ($allPostVars["saveArticle"] == "scheduleArticle") {
                $article["status"] = "scheduled";
            } else {
                $article["status"] = "draft";
            }

            if (!empty($allPostVars['featuredImageId'])) {
                $article["associatedMedia"]["featuredImage"]["id"]      = $allPostVars['featuredImageId'];
                $article["associatedMedia"]["featuredImageCaption"]     = $allPostVars['featured_image_descriptionText'];
            }

            if (!empty($allPostVars['featuredVideoId'])) {
                if (substr( $allPostVars['featuredVideoId'], 0, 4 ) === "http") {
                    // youtube video
                    $mediafile["name"]                                      = $allPostVars['featured_video_descriptionText'];
                    $mediafile["description"]                               = $allPostVars['featured_video_descriptionText'];
                    $mediafile["contentUrl"]                                = $allPostVars['featuredVideoId'];
                    $mediafile["dateCreated"]                               = date('c');
                    $mediafile["copyrightHolder"]["type"]                   = "direct";
                    $mediafile["copyrightHolder"]["value"]                  = $allPostVars['featured_video_copyrightHolder'];
                    $mediafile["encodingFormat"]                            = "video/youtube";
                    $media = $this->deployd->post("media", $mediafile);
                    $article["associatedMedia"]["featuredVideo"]["id"]      = $media['id'];
                    $article["associatedMedia"]["featuredVideoCaption"]     = $allPostVars['featured_video_descriptionText'];
                } else {
                    // mp4 video
                    $article["associatedMedia"]["featuredVideo"]["id"]      = $allPostVars['featuredVideoId'];
                    $article["associatedMedia"]["featuredVideoCaption"]     = $allPostVars['featured_video_descriptionText'];
                }
            }

            $article = $this->deployd->post("news", $article);

            return $response->withRedirect("/".$language."/edit/news/".$article["id"]);

        } else {
            // unknown request
        }
    }

    public function deleteNews(\Slim\Http\Request $request, \Slim\Http\Response $response, $args)
    {
        $request  = new \Slim\Http\MobileRequest($request);
        $response = new \Slim\Http\MobileResponse($response);

        if ($request->isMobile()) {
            $isMobile = true;
        } else {
            $isMobile = false;
        }

        $id         = $args['id'];
        $language   = $args['language'];

        $data       = ['isMobile' => $isMobile, 'language' => $language];
        $this->view->render($response, 'article.twig', $data);
        return $response;
    }

}
