<?php

namespace App\Controller;

use App\Entity\Show;
use App\Form\ShowType;
use App\Repository\ShowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/show")
 */
class ShowController extends AbstractController
{
    /**
     * @Route("/", name="show_index", methods={"GET"})
     */
    public function index(ShowRepository $showRepository): Response
    {
        return $this->render('show/index.html.twig', [
            'shows' => $showRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="show_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $show = new Show();
        $form = $this->createForm(ShowType::class, $show);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $file=$form->get('illustration')->getData();
            $fileEx=$file->guessExtension();
            $imageName=$show->getIllustration();
            $newFilename =$imageName.'.'.$fileEx;
            $file->move(
                $this->getParameter('image_directory'),
                $newFilename
            );
            $show->setIllustration($newFilename);
            $entityManager->persist($show);
            $entityManager->flush();

            return $this->redirectToRoute('show_index');
        }

        return $this->render('show/new.html.twig', [
            'show' => $show,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="show_show", methods={"GET"},requirements = {"id": "\d+"})
     */
    public function show(Show $show): Response
    {
        return $this->render('show/show.html.twig', [
            'show' => $show,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/edit", name="show_edit", methods={"GET","POST"},requirements = {"id": "\d+"})
     */
    public function edit(Request $request, Show $show): Response
    {
        $form = $this->createForm(ShowType::class, $show);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('show_index');
        }

        return $this->render('show/edit.html.twig', [
            'show' => $show,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="show_delete", methods={"DELETE"},requirements = {"id": "\d+"})
     */
    public function delete(Request $request, Show $show): Response
    {
        if ($this->isCsrfTokenValid('delete'.$show->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($show);
            $entityManager->flush();
        }

        return $this->redirectToRoute('show_index');
    }
}
