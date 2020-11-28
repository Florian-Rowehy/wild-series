<?php

namespace App\Controller;

use App\Entity\Program;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/programs", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render('program/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    /**
     * @route(
     *     "/{id}",
     *     name="show",
     *     requirements={"id"="\d+"},
     *     methods={"GET"}
     *     )
     *
     * @param $id
     * @return Response
     */
    public function show($id): Response
    {
        if (!$id) {
            throw $this
                ->createNotFoundException('No id has been sent to find a program in program\'s table.');
        }
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->find($id);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$id.' title, found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }

    /**
     * @route(
     *     "/category/{category}",
     *     name="show_category",
     *     requirements={"category"="^[a-z]+$"},
     *     methods={"GET"}
     *     )
     * @param $category
     * @return Response
     */
    public function showByCategory($category)
    {
        if (!$category) {
            throw $this
                ->createNotFoundException('No category argument');
        }

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findByCategory($category);

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program with '.$category.' category found \'program\' table.'
            );
        }
        return $this->render('program/category.html.twig', [
            'programs' => $programs,
            'category' => $category,
        ]);
    }
}
