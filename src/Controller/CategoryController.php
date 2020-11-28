<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller
 * @Route("/categories", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route(
     *     "/{categoryName}",
     *     name="show",
     *     requirements={"categoryName"="^[a-z]+$"},
     *     methods={"GET"}
     *     )
     * @param string $categoryName
     */
    public function show(string $categoryName)
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category argument');
        }

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findByCategory($categoryName);

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program with '.$categoryName.' category found in \'program\' table.'
            );
        }
        return $this->render('category/show.html.twig', [
            'programs' => $programs,
            'category' => $categoryName,
        ]);
    }
}
