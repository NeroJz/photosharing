<?php
/**
 * Created by PhpStorm.
 * User: MacBookPro
 * Date: 08/03/16
 * Time: 12:44 PM
 */

namespace Photo\ShareBundle\Controller;


use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PhotoController extends Controller
{

    public function indexAction(){
        $albums = $this->getDoctrine()->getRepository('ShareBundle:Album')
            ->findAll();


        return $this->render('ShareBundle:Photo:index.html.twig',array(
            'home' => true,
            'albums' => $albums
        ));
    }

    public function showGalleriesAction($album){
        $entity = $this->getDoctrine()->getRepository('ShareBundle:Album')
            ->find($album);

        if(!$entity){
            throw new EntityNotFoundException('Album not found!');
        }


        return $this->render('ShareBundle:Photo:galleries.html.twig',array(
            'home' => true,
            'album' => $entity
        ));
    }

}