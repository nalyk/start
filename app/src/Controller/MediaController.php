<?php
namespace App\Controller;

final class MediaController
{
    private $view;
    private $logger;
    private $deployd;

    public function __construct(\Slim\Views\Twig $view, \Psr\Log\LoggerInterface $logger, $deployd)
    {
        $this->view     = $view;
        $this->logger   = $logger;
        $this->deployd  = $deployd;
        $this->s3       = new \Aws\S3\S3Client([
            'version'                   => 'latest',
            'region'                    => 'us-east-1',
            'credentials'               => ['key' => '792XVHXUWKKJ8CQW1U0Y', 'secret' => 'gIQMdhG2IxSLfafySZgOyMYL42OlL9YWHIya3+Lz'],
            'endpoint'                  => 'https://s3.ungheni.today',
            'use_path_style_endpoint'   => true,
            'force_path_style'          => true
        ]);

        $this->ffmpeg = \FFMpeg\FFMpeg::create(array(
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout'          => 3600, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ), $logger);

        $this->ffprobe = \FFMpeg\FFProbe::create(array(
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout'          => 3600, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ), $logger);
    }

    public function uploadMediaRoute(\Slim\Http\Request $request, \Slim\Http\Response $response, $args)
    {
        $request  = new \Slim\Http\MobileRequest($request);
        $response = new \Slim\Http\MobileResponse($response);

        if ($request->isMobile()) {
            $isMobile = true;
        } else {
            $isMobile = false;
        }

        $allUploadedFiles = $request->getUploadedFiles();
        $allPostVars = $request->getParsedBody();
        if (isset($allUploadedFiles['file'])) {
            $uploadObj  = $allUploadedFiles['file'];
            $file       = $uploadObj->file;
            $fileHash   = md5_file($file);
            $filename   = $uploadObj->getClientFilename();
            $filemime   = mime_content_type($file);
            if (strpos($filemime, 'image') !== false) {
                $imgInfo        = getimagesize($file);
                $uploadfolder   = "images";
            }
            if (strpos($filemime, 'video') !== false) {
                $video["duration"]  = $this->ffprobe->streams($file)->videos()->first()->get('duration');
                $video_dimensions   = $this->ffprobe->streams($file)->videos()->first()->getDimensions();
                $video["width"]     = $video_dimensions->getWidth();
                $video["height"]    = $video_dimensions->getHeight();
                $uploadfolder       = "videos";
            }
        } else {
            copy($allPostVars['imageurl'], '/tmp/tmp-image-s3-upload');
            $file = '/tmp/tmp-image-s3-upload';
            $fileHash       = md5_file($file);
            $filemime       = mime_content_type($file);
            $imgInfo        = getimagesize($file);
            $fileurl        =   parse_url($allPostVars['imageurl']);
            $url_path       = explode('/',$fileurl['path']); // explode the path part
            $filename       =  $url_path[count($url_path)-1];
            $uploadfolder   = "images";
        }
        
        try {
            $upload = $this->s3->putObject([
                'Bucket' => 'ungheni',
                'Key'    => $uploadfolder.'/'.date('Y/m/d/').'news-'.str_replace(' ', '_', strtolower($filename)),
                'Body'   => fopen($file, 'r')
            ]);
        } catch (Aws\S3\Exception\S3Exception $e) {
            echo "There was an error uploading the file.\n";
        }

        $mediafile["name"]          = $filename;
        $mediafile["description"]   = $filename;
        $mediafile["contentUrl"]    = $upload['ObjectURL'];
        $mediafile["dateCreated"]   = date('c');
        $mediafile["hash"]          = $fileHash;

        if (strpos($filemime, 'image') !== false) {
            $mediafile["width"]             = $imgInfo[0];
            $mediafile["height"]            = $imgInfo[1];
            $mediafile["encodingFormat"]    = $filemime;
        }
        if (strpos($filemime, 'video') !== false) {
            $mediafile["width"]             = $video["width"];
            $mediafile["height"]            = $video["height"];
            $mediafile["encodingFormat"]    = $filemime;
        }
       
        $media = $this->deployd->get("media", null, '{"hash":"'.$fileHash.'"}');
        if (isset($media[0]["id"])) {
            $media = $media[0];
        } else {
            $media = $this->deployd->post("media", $mediafile);
            if (strpos($filemime, 'image') !== false) {
                $postcrop["url"] = $mediafile["contentUrl"];
                $crop["associatedMedia"] = $this->deployd->post("crop", $postcrop);
                $media = $this->deployd->put("media", $media["id"], $crop);
            }
        }

        $response->withHeader('Content-Type', 'application/json');
        $response->write(json_encode($media));
        return $response;
    }
}