<?php

namespace NumoBundle\Repository;

use NumoBundle\Entity\User;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends \Doctrine\ORM\EntityRepository
{
    public function getEventList(User $curentUser)
    {
        return [];
    }
}