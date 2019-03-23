<?php

namespace App\DataFixtures;

use App\Entity\Auth\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DevFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // Client oAuth2
        $client_oauth = new Client();
        $client_oauth->setRandomId("10hitwtgqdesw4k8sc44wgsogcos8840owcso0ok04cwgskkwg");
        $client_oauth->setRedirectUris(["http://dev.heimdall.watch"]);
        $client_oauth->setSecret("1wk8ojwd5ts00osos8wssgkcowooowgs84444ocsc444wg0wcw");
        $client_oauth->setAllowedGrantTypes(["password", "refresh_token"]);
        $manager->persist($client_oauth);

        // Brahim
        $brahim = new User();
        $brahim->setUsername("Brahim")
               ->setEmail("sosthen.gaillard@gmail.com")
               ->setPassword($this->encoder->encodePassword($brahim, "brahim"))
               ->setRoles(['ROLE_ADMIN']);
        $manager->persist($brahim);

        // Student 1
        $sosthen = new User();
        $sosthen->setUsername("Sosthen")
               ->setEmail("sosthen.gaillard@gmail.com")
               ->setPassword($this->encoder->encodePassword($sosthen, "sosthen"))
               ->setRoles(['ROLE_STUDENT']);
        $manager->persist($sosthen);

        $manager->flush();
    }
}
