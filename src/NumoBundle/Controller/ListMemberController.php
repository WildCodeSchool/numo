<?php

namespace NumoBundle\Controller;

use NumoBundle\Entity\Published;
use NumoBundle\Entity\Event;
use NumoBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class ListMemberController extends Controller
{
    /**
     * @Route("/members", name="members")
     */
    public function showListMember()
    {
        $em = $this->getDoctrine()->getManager();
        $members = $em->getRepository('NumoBundle:User')->findAll();

        return $this->render('NumoBundle:site:member.html.twig', [
            'members' => $members
        ]);
    }

    /**
     * Finds and displays a user entity.
     * @Route("/profilMember/{id}", name="profilMember")
     */
    public function showProfilMember(User $user)
    {
        $api = $this->get('numo.apiopenagenda');
        $em = $this->getDoctrine()->getManager();
        $published = $em->getRepository('NumoBundle:Published')->findBy(['author' => $user->getId(), 'deleted' => 0]);
        $uids = [];
        foreach ($published as $pub) {
            $uids[] = $pub->getUid();

        }
        $oaevents = $api->getEvents($uids);

        return $this->render('NumoBundle:site:profilMember.html.twig', [
            'user' => $user,
            'uids' => $uids,
            'oaevents' => $oaevents,
        ]);
    }

}




