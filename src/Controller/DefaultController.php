<?php

namespace App\Controller;

use App\Form\SearchProgramType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig');
    }

    public function navbarTop(CategoryRepository $categoryRepository): Response
    {
        return $this->render('layout/navbartop.html.twig', [
            'categories' => $categoryRepository->findAll()
        ]);
    }

    public function navbarSearch(): Response
    {
        $form = $this->createForm(SearchProgramType::class);
        return $this->render('layout/searchbar.html.twig', [
            'searchForm' => $form->createView()
        ]);
    }
}
