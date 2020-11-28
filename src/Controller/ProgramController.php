<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Season;
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
     * @Route(
     *     "/programs/{programId}/seasons/{seasonId}",
     *     name="season_show",
     *     requirements={"programId"="^\d+$", "seasonId"="^\d+$"},
     *     methods={"GET"},
     *     )
     * @param int $programId
     * @param int $seasonId
     * @return Response
     */
    public function showSeason(int $programId, int $seasonId): Response
    {
        if (!$programId) {
            throw $this
                ->createNotFoundException('No id has been sent to find a program in program\'s table.');
        }
        if (!$seasonId) {
            throw $this
                ->createNotFoundException('No id has been sent to find a season in season\'s table.');
        }
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->find($programId);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$programId.' id, found in program\'s table.'
            );
        }
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->find($seasonId);
        if (!$season) {
            throw $this->createNotFoundException(
                'No season with '.$seasonId.' id found in season\'s table.'
            );
        }
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
                'No program with '.$id.' id, found in program\'s table.'
            );
        }
        $seasons = $program->getSeasons();
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }
}
