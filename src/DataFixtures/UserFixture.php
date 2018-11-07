<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

// The way you use BaseFixture class is still the same: 
//  extend BaseFixture and update the load() method to be protected function loadData() . 
//  remove the old use statement.

class UserFixture extends BaseFixture
{
    // Encoding Passwords
    private $passwordEncoder;
    
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function loadData(ObjectManager $manager)
    {

    	$this->createMany(10, 'main_users', function($i) {
    		$user = new User();
    		$user->setEmail(sprintf('spacebar%d@example.com', $i));
    		// We an use Faker, which we already setup inside
    		$user->setFirstName($this->faker->firstName);

            $user->setPassword($this->passwordEncoder->encodePassword( 
                $user,
                'engage'
            ));

    		return $user;
		});

        $manager->flush();
    }
}
