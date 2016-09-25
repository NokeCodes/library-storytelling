<?php

namespace RoanokeLibBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use RoanokeLibBundle\Entity\Story;
use RoanokeLibBundle\Entity\Media;


/**
 * Description of PullTwitter
 *
 * @author cwilkes
 */
class PullTwitter {
    
    public $container;
 
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
        
    public function pullTwitterStories($hashtag = "LSUvsAUB")
    {
        
        $em = $this->container->get("doctrine")->getManager();
            
        $twitter = $this->container->get('endroid.twitter');
        
        //$since_id = 779478701800955904;
        //for($i = 0; $i < 5; $i++)
        
        $tag = $hashtag;
        $q = $tag . " -filter:retweets";
        
        $cnt = 0;
        {

            $query = array(
              //"q" => "#Roanoke -filter:retweets",
              "q" => $q,
              "count" => 100,
              //"geocode" => "37.2681615,-79.9360483,1000mi",
            );

            $response = $twitter->query('search/tweets', 'GET', 'json', $query);
            $tweets = json_decode($response->getContent());
            //\Symfony\Component\VarDumper\VarDumper::dump($tweets);
            //exit();
            //if(count($tweets->statuses))
            //  $since_id = end($tweets->statuses)->id;
            //  
            //reverse to only get latest non retrieved
            //this could skip entries if more than 100 new tweets exist
            for($i = count($tweets->statuses)-1; $i >= 0; $i--)
            {
                $status = $tweets->statuses[$i];
                
                if($status->id > (int)$this->container->get('craue_config')->get($tag . "_max_id"))
                {
                    $this->container->get('craue_config')->set($tag . "_max_id", $status->id);
                    
                    echo $status->id . "<br/>";
                    
                    //\Symfony\Component\VarDumper\VarDumper::dump($status);
                    
                    $story = new Story();
                    $story->setName($status->user->name . " - @" . $status->user->screen_name);
                    $story->setTitle("Twitter, ".$status->created_at.", ".$status->id);
                    $story->setDescription($status->text);
                    if($status->geo && $status->geo->type == "Point")
                    {
                        $story->setLatitude($status->geo->coordinates[0]);
                        $story->setLongitude($status->geo->coordinates[1]);
                    }
                    if(isset($status->extended_entities->media))
                    {
                        $media = $status->extended_entities->media[0];
                        if($media->type == "photo")
                        {
                            echo $media->media_url;
                            $imageData = file_get_contents($media->media_url);
                            
                            $filesystem = $this->container->get('knp_gaufrette.filesystem_map')->get('story_media_fs');
                            $filename = basename(parse_url($media->media_url, PHP_URL_PATH));
                            $filesystem->write($filename, $imageData);
                            
                            $media = new Media();
                            $media->setName($filename);
                            $media->setMediaName($filename);
                            $media->setStory($story);
                            $story->addMedia($media);
                            
                            $em->persist($media);
                        }
                        else if($media->type == "video")
                        {
                            echo $media->expanded_url;
                            
                            $saveto = $this->container->getParameter("story_path") . '/' . uniqid() . '.mp4';
                            
                            $filename = basename($saveto);
                            
                            $cmd = 'youtube-dl -o "' . $saveto .'" ' . $media->expanded_url;
                            $ret;
                            $output;
                            exec($cmd, $output, $ret);
                            
                            echo 'Output: ' . var_dump($output);
                            echo 'Return: ' . $ret;
                            
                            if($ret == 0)
                            {
                                 
                                $media = new Media();
                                $media->setName($filename);
                                $media->setMediaName($filename);
                                $media->setStory($story);
                                $story->addMedia($media);

                                $em->persist($media);
                            }
                        }
                    }
                    
                    $em->persist($story);
                    $em->flush();
                    
                    $cnt++;
                }
                
                if($status->id == 779885620105588736) //779885620105588736) //779886301013311488)
                {
                //echo \Symfony\Component\VarDumper\VarDumper::dump($status->extended_entities->media);
                }
                if($status->id == 779886301013311488) //779885620105588736) //779886301013311488)
                {
                //echo \Symfony\Component\VarDumper\VarDumper::dump($status->extended_entities->media);
                }
            //echo $status->id . "<br/>";
            //$since_id = $status->id;
            }
        }
        
        $out = $cnt . " new tweets added";
        echo $out;
        return new \Symfony\Component\HttpFoundation\Response($out);
    }
}
