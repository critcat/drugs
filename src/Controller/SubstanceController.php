<?php

namespace App\Controller;

use App\Entity\Substance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SubstanceController
 * @Route("/substance")
 */
class SubstanceController extends AbstractController
{
    /**
     * @Route("/new", name="substance_new")
     */
    public function new(EntityManagerInterface $em): Response
    {
        $substance = new Substance();
        $substance->setName('Cocaine');

        $em->persist($substance);
        $em->flush();

        return new Response(sprintf(
            'Well hallo! The shiny new substance is id #%d, name: %s',
            $substance->getId(),
            $substance->getName()
        ));
    }
}