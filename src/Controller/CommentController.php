<?php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route(
 *     "/comments",
 *     name="comment_"
 * )
 */
class CommentController extends AbstractController {

    /**
     * @Route(
     *     "/{id}",
     *     name="delete",
     *     methods={"DELETE"}
     * )
     * @Security("is_granted('ROLE_ADMIN') or user == comment.getAuthor()")
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        $referer = filter_var($request->headers->get('referer'), FILTER_SANITIZE_URL);
        return $this->redirect($referer);
    }
}