<?php

namespace RoanokeLibBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use RoanokeLibBundle\Entity\Story;
use RoanokeLibBundle\Entity\Media;

use RoanokeLibBundle\Form\StoryType;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * Story controller.
 *
 * @Route("/story")
 */
class StoryController extends Controller
{
    /**
     * Lists all Story entities.
     *
     * @Route("/", name="story_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stories = $em->getRepository('RoanokeLibBundle:Story')->findBy(array(), array('created' => 'desc'));

        return $this->render('story/index.html.twig', array(
            'stories' => $stories,
        ));
    }

    /**
     * Creates a new Story entity.
     *
     * @Route("/new", name="story_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $story = new Story();
        $form = $this->createForm('RoanokeLibBundle\Form\StoryType', $story);
        $form->handleRequest($request);
        
        $media = new Media();
        $media_form = $this->createForm('RoanokeLibBundle\Form\MediaType', $media);
        $media_form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $media_form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if($media->getMediaFile())
            {
                $story->addMedia($media);
                $media->setStory($story);
                $media->setName($media->getMediaFile()->getFilename());
                
                $em->persist($media);
            }
            $em->persist($story);
            $em->flush();

            return $this->redirectToRoute('thankyou');
        }

        return $this->render('story/new.html.twig', array(
            'story' => $story,
            

            'form' => $form->createView(),
            'media_form' => $media_form->createView(),
        ));
    }

    /**
     * Creates a new Story entity.
     *
     * story[name]
     * story[title]
     * story[description]
     * story[latitude]
     * story[longitude]
     * media[mediaFile][file]
     * 
     * @Route("/api/new", name="story_api_new")
     * @Method({"POST"})
     */
    public function apiNewAction(Request $request)
    {
        $story = new Story();
        $form = $this->createForm('RoanokeLibBundle\Form\StoryType', $story);
        $form->handleRequest($request);
        
        $media = new Media();
        $media_form = $this->createForm('RoanokeLibBundle\Form\MediaType', $media);
        $media_form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $media_form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if($media->getMediaFile())
            {
                $story->addMedia($media);
                $media->setStory($story);
                $media->setName($media->getMediaFile()->getFilename());
                
                $em->persist($media);
            }
            $em->persist($story);
            $em->flush();

            return $this->redirectToRoute('thankyou');
        }

        return $this->render('story/new.html.twig', array(
            'story' => $story,
            

            'form' => $form->createView(),
            'media_form' => $media_form->createView(),
        ));
    }
    
    

    /**
     * 
     * @Route("/pull/twitter", name="story_pull_twitter")
     * @Method({"GET"})
     */
    public function pullTwitterAction(Request $request)
    {
        $pullTwitter = $this->container->get("roanokelib.pull_twitter");
        return $pullTwitter->pullTwitterStories();
         
    }
    
    /**
     * Finds and displays a Story entity.
     *
     * @Route("/{id}", name="story_show")
     * @Method("GET")
     */
    public function showAction(Story $story)
    {
        $deleteForm = $this->createDeleteForm($story);
        
        return $this->render('story/show.html.twig', array(
            'story' => $story,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    
    /**
     * @Route("/downloadMedia/{id}", name="story_media_download")
     * @Method("GET")
     */
    public function downloadMediaAction(Media $media)
    {
        $downloadHandler = $this->get('vich_uploader.download_handler');
        
        return $downloadHandler->downloadObject($media, $fileField = 'mediaFile');
    }

    /**
     * Displays a form to edit an existing Story entity.
     *
     * @Route("/{id}/edit", name="story_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Story $story)
    {
        $deleteForm = $this->createDeleteForm($story);
        $editForm = $this->createForm('RoanokeLibBundle\Form\StoryType', $story);
        $editForm->handleRequest($request);
        

        $media = new Media();
        $media_form = $this->createForm('RoanokeLibBundle\Form\MediaType', $media);
        $media_form->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($story);
            
            if($media->getMediaFile())
            {
                $story->addMedia($media);
                $media->setStory($story);
                $media->setName($media->getMediaFile()->getFilename());
                
                $em->persist($media);
            }
            $em->flush();

            return $this->redirectToRoute('story_edit', array('id' => $story->getId()));
        }

        return $this->render('story/edit.html.twig', array(
            'story' => $story,
            'form' => $editForm->createView(),
            'media_form' => $media_form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Story entity.
     *
     * @Route("/{id}", name="story_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Story $story)
    {
        $form = $this->createDeleteForm($story);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($story);
            $em->flush();
        }

        return $this->redirectToRoute('story_index');
    }

    /**
     * Creates a form to delete a Story entity.
     *
     * @param Story $story The Story entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Story $story)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('story_delete', array('id' => $story->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
