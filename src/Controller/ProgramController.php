<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use App\Form\SearchProgramType;
use App\Repository\ProgramRepository;
use App\Service\Slugify;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/programs", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, ProgramRepository $programRepository): Response
    {
        $form = $this->createForm(SearchProgramType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $programs = $programRepository->findByInput($search);
        } else {
            $programs = $programRepository->findAll();
        }

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
     */
    public function new(EntityManagerInterface $entityManager, Request $request, Slugify $slugify, MailerInterface $mailer): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program, [
            'action' => $this->generateUrl('program_new'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($program->getTitle());
            $program
                ->setSlug($slug)
                ->setCreator($this->getUser());
            $entityManager->persist($program);
            $entityManager->flush();

            $this->addFlash('success', 'La nouvelle série a bien été créée');

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('Program/newProgramEmail.html.twig', ['program' => $program]));
            $mailer->send($email);

            return $this->redirectToRoute("program_show", ["slug" => $slug]);
        }

        return $this->render('program/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @route(
     *     "/{programSlug}/edit",
     *     name="edit",
     *     requirements={"programSlug"="^[a-z-]+$"},
     *     methods={"GET", "PUT"}
     * )
     * @ParamConverter("program", class=Program::class, options={"mapping": {"programSlug": "slug"}})
     */
    public function edit(Program $program, EntityManagerInterface $entityManager, Request $request, Slugify $slugify): Response
    {
        if (!($this->getUser() == $program->getCreator()||$this->isGranted('ROLE_ADMIN'))) {
            // If not the owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Only the owner can edit the program!');
        }
        $form = $this->createForm(ProgramType::class, $program, [
            'action' => $this->generateUrl('program_edit', ['programSlug'=> $program->getSlug()]),
            'method' => 'PUT',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $entityManager->persist($program);
            $entityManager->flush();

            return $this->redirectToRoute("program_show", ["slug" => $slug]);
        }

        return $this->render('program/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/programs/{programSlug}/seasons/{seasonId}/episodes/{episodeId}",
     *     name="episode_show",
     *     requirements={"programSlug"="^[a-z-]+$", "seasonId"="^\d+$", "episodeId"="^\d+$"},
     *     methods={"GET"}
     * )
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programSlug": "slug"}})
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
     *     "/programs/{programSlug}/seasons/{season}",
     *     name="season_show",
     *     requirements={"programSlug"="^[a-z-]+$", "season"="^\d+$"},
     *     methods={"GET"},
     * )
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programSlug": "slug"}})
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
     *     "/{slug}",
     *     name="show",
     *     requirements={"slug"="^[a-z-]+$"},
     *     methods={"GET"}
     *     )
     */
    public function show(Program $program): Response
    {
        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }

    /**
     * @Route(
     *     "/{id}",
     *     name="delete",
     *     requirements={"id"="^\d+$"},
     *     methods={"DELETE"}
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Program $program): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($program);
            $entityManager->flush();
            $this->addFlash('danger', 'La série a bien été supprimée');
        }

        return $this->redirectToRoute('program_index');
    }

    /**
     * @Route(
     *     "/search/{title}",
     *     name="search_program_autocomplete",
     *     methods={"GET"}
     * )
     */
    public function autoCompleteSearchBar(ProgramRepository $programRepository, SerializerInterface $serializer, string $title)
    {
        $programs = $programRepository->findByInput($title);
        $jsonObject = $serializer->serialize($programs, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['category', 'seasons', 'actors', 'creator']]);

        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route(
     *     "/{id}/watchlist",
     *     name="watchlist",
     *     methods={"GET"}
     * )
     */
    public function addToWatchList(EntityManagerInterface $entityManager, Program $program)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user =  $this->getUser();
        if ($user->isInWatchlist($program)) {
            $user->removeWatchlist($program);
        } else {
            $user->addWatchlist($program);
        }

        $entityManager->flush();
        return $this->json([
            'isInWatchlist' => $user->isInWatchlist($program)
        ]);
    }
}
