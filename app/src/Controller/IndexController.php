<?php
namespace App\Controller;

final class IndexController
{
    private $view;
    private $logger;
    private $deployd;

    public function __construct(\Slim\Views\Twig $view, \Psr\Log\LoggerInterface $logger, $deployd)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->deployd = $deployd;
    }

    public function __invoke(\Slim\Http\Request $request, \Slim\Http\Response $response, $args)
    {
        $this->logger->warning(substr(strrchr(rtrim(__CLASS__, '\\'), '\\'), 1).': '.__FUNCTION__);

        $request  = new \Slim\Http\MobileRequest($request);
        $response = new \Slim\Http\MobileResponse($response);

        if ($request->isMobile()) {
            $isMobile = true;
        } else {
            $isMobile = false;
        }
        
        $data = ['activeObject' => 'page', 'activeName' => 'home', 'isMobile' => $isMobile];

        $this->view->render($response, 'base.twig', $data);
        return $response;
    }
}
