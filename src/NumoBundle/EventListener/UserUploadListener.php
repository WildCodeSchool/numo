<?php
/**
 * Created by PhpStorm.
 * User: fanny
 * Date: 07/06/17
 * Time: 12:00
 */

namespace NumoBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use NumoBundle\Entity\User;
use NumoBundle\Services\UserUploader;
use Symfony\Component\HttpFoundation\RequestStack;

class UserUploadListener
{
    private $uploader;
    private $oldFile;

    public function __construct(UserUploader $uploader, RequestStack $requestStack)
    {
        $this->uploader = $uploader;
        $this->requestStack = $requestStack;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->oldFile) {
            $entity->setImageUrl($this->oldFile);

        } else {
            $this->uploadFile($entity);
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            return;
        }
        $masterRequest = $this->requestStack->getMasterRequest()->get('_route');
        if($masterRequest == 'fos_user_profile_edit'){
            $this->oldFile=$entity->getImageUrl();
            if ($fileName = $entity->getImageUrl()) {
                $entity->setImageUrl(new File($this->uploader->getTargetDir() . '/' . $fileName));
            }
        }

    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof User) {
            return;
        }
        if(is_file($entity->getImageUrl())) {
            unlink($entity->getImageUrl());
        }
    }


    private function uploadFile($entity)
    {
        // upload only works for Product entities
        if (!$entity instanceof User) {
            return;
        }

        $file = $entity->getImageUrl();

        // only upload new files
        if (!$file instanceof UploadedFile) {
            return;
        }

        $fileName = $this->uploader->upload($file);
        $entity->setImageUrl($fileName);
    }
}