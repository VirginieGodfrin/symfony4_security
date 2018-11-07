<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// third way to deny access controll : Protecting an Entire Controller Class
/**
 * @IsGranted("ROLE_ADMIN") 
 */
class CommentAdminController extends Controller
{
    // second way to deny access controll : IsGranted Annotation
    /**
     * @Route("/admin/comment", name="comment_admin")
     * @IsGranted("ROLE_USER")
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
