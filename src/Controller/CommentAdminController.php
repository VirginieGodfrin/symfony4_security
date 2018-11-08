<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// third way to deny access controll : Protecting an Entire Controller Class
// name the rôle with collection name and not with user "activity"
/**
 * @IsGranted("ROLE_ADMIN_COMMENT") 
 */
class CommentAdminController extends Controller
{
    // second way to deny access controll : IsGranted Annotation - @IsGranted("ROLE_USER")
    /**
     * @Route("/admin/comment", name="comment_admin")
     */
    public function index(CommentRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        // first easy way to deny access controll
        // $this->denyAccessUnlessGranted('ROLE_USER');
        $q = $request->query->get('q');

        $queryBuilder = $repository->getWithSearchQueryBuilder($q);

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('comment_admin/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
