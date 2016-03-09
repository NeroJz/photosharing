<?php
/**
 * Created by PhpStorm.
 * User: MacBookPro
 * Date: 08/03/16
 * Time: 2:25 PM
 */

namespace Photo\ShareBundle\Controller;


use Doctrine\ORM\EntityNotFoundException;
use Photo\ShareBundle\Entity\Album;
use Photo\ShareBundle\Entity\Photo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{

    public function indexAction(Request $request)
    {
        $albums = $this->getDoctrine()->getRepository('ShareBundle:Album')
            ->findAll();


        $album = new Album();

        $form = $this->createFormBuilder($album)
            ->add('name')
            ->add('description')
            ->add('file')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($album);

            $em->flush();

            return $this->redirect($this->generateUrl('photo_home'));

        }


        return $this->render("ShareBundle:Dashboard:index.html.twig",
            array(
                "dashboard" => true,
                "form" => $form->createView(),
                "albums" => $albums
            ));
    }

    public function uploadPhotoAction(Request $request, $album)
    {
        $album = $this->getDoctrine()->getRepository('ShareBundle:Album')
            ->find($album);

        $photo = new Photo();

        $form = $this->createFormBuilder($photo)
            ->add('multiphotos', 'file', array(
                'attr' => array(
                    'multiple' => true,
                )
            ))
            ->getForm();

        $form->handleRequest($request);


        if ($form->isValid() && $form->isSubmitted()) {

            $files = $request->files->get('files');
            //var_dump($files);
            $em = $this->getDoctrine()->getManager();
            foreach($files as $file){
                $photo = new Photo();
                $photo->setAlbum($album);
                $photo->setFile($file);

                $em->persist($photo);
                $em->flush();
            }
            return $this->redirect($this->generateUrl('upload', array('album' => $album->getId())));
        }

        return $this->render('ShareBundle:Dashboard:upload.html.twig', array(
            'dashboard' => true,
            'album' => $album,
            'form' => $form->createView(),
        ));
    }

    public function deleteAlbumAction($album)
    {
        $album = $this->getDoctrine()->getRepository('ShareBundle:Album')
            ->find($album);

        if (!$album) {
            throw new EntityNotFoundException("Album is not found!");
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($album);
        $em->flush();

        return $this->redirect($this->generateUrl('dashboard'));
    }

    public function deletePhotoAction($photo, $album){
        $photo = $this->getDoctrine()->getRepository('ShareBundle:Photo')
            ->find($photo);

        if(!$photo){
            throw new EntityNotFoundException("Photo is not found!");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($photo);
        $em->flush();

        return $this->redirect($this->generateUrl('upload', array('album' => $album)));
    }

}