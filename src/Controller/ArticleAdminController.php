<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class ArticleAdminController extends AbstractController
{
    /**
     * @Route("/admin/article/new", name="admin_article_new")
     * @IsGranted("ROLE_ADMIN_ARTICLE") 
     */
    public function new(EntityManagerInterface $em)
    {
        die('todo');

        return new Response(sprintf(
            'Hiya! New Article id: #%d slug: %s',
            $article->getId(),
            $article->getSlug()
        ));
    }

    /**
     * @Route("/admin/article/{id}/edit") 
     */
    public function edit(Article $article)
    {
        // voter:  I'm using the same isGranted() function as before. 
        // But instead of passing a role, I'm just "inventing" a string: MANAGE . 
        // It also turns out that isGranted() has an optional second argument: 
        // a piece of data that is relevant to making this access decision.
        // read the doc for more explain 
        if (!$this->isGranted('MANAGE', $article)) {
            throw $this->createAccessDeniedException('No access!');
        }
        dd($article);
    }
}
