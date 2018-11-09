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
        // rememeber : Because Article is an entity, SensioFrameworkExtraBundle 
        // - a bundle we installed a long time ago - will use the {id} route parameter to query for the correct Article .
        
        // I want to allow access if you have ROLE_ADMIN_ARTICLE or if you are the author of this Article .
        // enforce the security logic
        // The $this->isGranted() method is new to us, but simple: 
        // it returns true or false based on whether or not the user has ROLE_ADMIN_ARTICLE . 
        // createAccessDeniedException() : that method is just a shortcut to call $this->isGranted() 
        // and then throw $this->createAccessDeniedException() if that returned false
        // The cool takeaway is that, the way you ultimately deny access in Symfony is by throwing a special exception object 
        // that this method creates. Oh, and the message - No access! - that's only shown to developers.
        if ($article->getAuthor() != $this->getUser() && !$this->isGranted('ROLE_ADMIN_ARTICLE')) { 
            throw $this->createAccessDeniedException('No access!');
        }
        dd($article);
        // I don't want this important logic to live in my controller. 
        // Why not? What if I need to re-use this somewhere else? 
        // Duplicating security logic is a bad idea. 
        // And, what if I need to use it in Twig to hide or show an edit link? 
        // That would really be ugly.
        // Nope, there's a better way: a wonderful system called voters.
    }
}
