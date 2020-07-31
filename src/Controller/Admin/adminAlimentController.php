<?php

namespace App\Controller\Admin;

use App\Entity\Aliment;
use App\Form\AlimentType;
use App\Repository\AlimentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class adminAlimentController extends AbstractController
{
    /**
     * @Route("/admin/aliment", name="adminAliment")
     */
    public function index(AlimentRepository $repository)
    {
        $aliments = $repository->findAll();
        return $this->render('admin/admin_aliment/adminAliment.html.twig', [
            "aliments" => $aliments
        ]);
    }

    /**
     * @Route("/admin/aliment/creation", name="adminAlimentCreation")
     * @Route("/admin/aliment/{id}", name="adminAlimentModification",methods="GET|POST")
     */
    public function ajoutEtModification(Aliment $aliment = null, Request $request, EntityManagerInterface $entityManager)
    {
        if (!$aliment) {
            $aliment = new Aliment;
        }

        $form = $this->createForm(AlimentType::class, $aliment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $modif = $aliment->getId() !== null;
            $entityManager->persist($aliment);
            $entityManager->flush();
            $this->addFlash("success",($modif) ? "La modification à été effectuée" : "L'ajout à été effectué");
            return $this->redirectToRoute('adminAliment');
        }
        return $this->render('admin/admin_aliment/adminAlimentAjoutEtModif.html.twig', [
            "aliment" => $aliment,
            "form" => $form->createView(),
            "isModification" => $aliment->getId() !== null
        ]);
    }

    /**
     * @Route("/admin/aliment/{id}", name="adminAlimentSuppression",methods="delete")
     */
    public function suppression(Aliment $aliment, Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->isCsrfTokenValid("SUP" . $aliment->getId(), $request->get('_token'))) {
            $entityManager->remove($aliment);
            $entityManager->flush();
            $this->addFlash("success","La suppression à été effectuée");
            return $this->redirectToRoute("adminAliment");
        }
    }
}
