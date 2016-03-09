<?php

namespace Photo\ShareBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Photo\ShareBundle\Entity\Album;

/**
 * Photo
 *
 * @ORM\Table(name="photo")
 * @ORM\Entity(repositoryClass="Photo\ShareBundle\Entity\PhotoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Photo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;


    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="Photo\ShareBundle\Entity\Album", inversedBy = "photos")
     */
    private $album;


    private $temp;


    private $multiphotos;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Photo
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Photo
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get absolute path
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    /**
     * Get web path for template link
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    /**
     * Get upload directory
     * @return string
     */
    public function getUploadRootDir(){
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    /**
     * Get directory name
     * @return string
     */
    public function getUploadDir(){
        return 'uploads/photos';
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $files
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        $this->name = $file->getClientOriginalName();

        if(isset($this->path)){
            $this->temp = $this->path;
            $this->path = null;
        }else{
            $this->path = "initial";
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload(){
        if(null !== $this->getFile()){
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename . '.' . $this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload(){
        if(null === $this->getFile()){
            return;
        }

        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->path
        );

        if(isset($this->temp)){
            unlink($this->getUploadRootDir() . '/' . $this->temp);
            $this->temp = null;
        }

        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload(){
        $file = $this->getAbsolutePath();

        if($file){
            unlink($file);
        }
    }

    /**
     * @return mixed
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * @param mixed $album
     */
    public function setAlbum(Album $album)
    {
        $this->album = $album;
    }

    /**
     * @return mixed
     */
    public function getMultiphotos()
    {
        return $this->multiphotos;
    }

    /**
     * @param mixed $multiphotos
     */
    public function setMultiphotos(UploadedFile $multiphotos = null)
    {
        $this->multiphotos = $multiphotos;
    }

}
