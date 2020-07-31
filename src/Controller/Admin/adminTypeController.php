<?php

namespace App\Controller\Admin;

use App\Entity\Type;
use App\Form\TypesType;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class adminTypeController extends AbstractController
{
    /**
     * @Route("/admin/admin/type", name="adminTypes")
     */
    public function index(TypeRepository $repository)
    {
        $types = $repository->findAll();
        return $this->render('admin/admin_type/adminTypes.html.twig',[
            "types" => $types
        ]);
    }

    /**
     * @Route("/admin/admin/type/create", name="adminTypesAjout")
     * @Route("/admin/admin/type/{id}", name="adminTypesModif", methods="POST|GET")
     */
    public function ajoutEtModif(Type $type = null, Request $request, EntityManagerInterface $entityManager)
    {
        if(!$type) {
            $type = new Type();
        }

        $form = $this->createForm(TypesType::class, $type, );

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $modif = $type->getId() !== null;
            $entityManager->persist($type);
            $entityManager->flush();
            $this->addFlash('success',($modif) ? "La modification à été effectuée" : "L'ajout à été effectué");
            return $this->redirectToRoute("adminTypes");
        }
        return $this->render('admin/admin_type/adminTypesAjoutEtModif.html.twig',[
            "type" => $type,
            "form" => $form->createView(),
            "isModification" => $type->getId() !== null
        ]);
    }

    /**
     * @Route("/admin/admin/type/{id}", name="adminTypesSuppression", methods="delete")
     */
    public function suprression(Type $type, EntityManagerInterface $entityManager, Request $request)
    {
        if($this->isCsrfTokenValid('SUP' . $type->getId(), $request->get('_token'))) {
            $entityManager->remove($type);
            $entityManager->flush();
            $this->addFlash("success","La suppression à été effectuée");
            return $this->redirectToRoute("adminTypes");
        }
    }
}
