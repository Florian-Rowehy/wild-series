<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

        return $this->render('program/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    /**
     * @Route(
     *     "/new",
     *     name="new",
     *     methods={"GET", "POST"}
     * )
     * @route(
     *     "/{id}/edit",
     *     name="edit",
     *     requirements={"id"="^\d+$"},
     *     methods={"GET", "POST"}
     * )
     */
    public function form(Program $program = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$program)
            $program = new Program();

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($program);
            $entityManager->flush();
            return $this->redirectToRoute("program_show", ["id" => $program->getId()]);
        }
        return $this->render('program/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/programs/{programId}/seasons/{seasonId}/episodes/{episodeId}",
     *     name="episode_show",
     *     requirements={"programId"="^\d+$", "seasonId"="^\d+$", "episodeId"="^\d+$"},
     *     methods={"GET"}
     * )
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episodeId": "id"}})
     */
    public function  showEpisode(Program $program, Season $season, Episode $episode): Response
    {
        return $this->render('program/episode_show.html.twig', [
            'program'=>$program,
            'season'=>$season,
            'episode'=>$episode,
        ]);
    }

    /**
     * @Route(
     *     "/programs/{program}/seasons/{season}",
     *     name="season_show",
     *     requirements={"program"="^\d+$", "season"="^\d+$"},
     *     methods={"GET"},
     * )
     */
    public function showSeason(Program $program, Season $season): Response
    {
        $episodes = $season->getEpisodes();

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }

    /**
     * @route(
     *     "/{id}",
     *     name="show",
     *     requirements={"id"="^\d+$"},
     *     methods={"GET"}
     *     )
     */
    public function show(Program $program): Response
    {
        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }
}
