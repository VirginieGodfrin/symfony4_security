<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;

// Why base controller ? autoclompletion with phpstrom and 
// add other shortcut methods specific to my app. 
// If there's something that you do frequently, but it doesn't make sense to move that logic into a service, 
// just add a new protected function .

/**
 * @method User|null getUser() 
 */
abstract class BaseController extends AbstractController
{
	protected function getUser(): User 
	{
		return parent::getUser(); 
	}

}