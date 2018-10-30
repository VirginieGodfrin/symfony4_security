<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;

// The way you use BaseFixture class is still the same: 
//  extend BaseFixture and update the load() method to be protected function loadData() . 
//  remove the old use statement.

class UserFixture extends BaseFixture
{
    protected function loadData(ObjectManager $manager)
    {
    	// create 10 user, main_users = group name
    	// This will be called 10 times
    	$this->createMany(10, 'main_users', function($i) {
    		$user = new User();
    		$user->setEmail(sprintf('spacebar%d@example.com', $i));
    		// We an use Faker, which we already setup inside
    		$user->setFirstName($this->faker->firstName);
    		return $user;
		});
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
