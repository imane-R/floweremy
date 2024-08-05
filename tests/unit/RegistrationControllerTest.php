<?php

namespace App\Tests\unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use App\DataFixtures\AppFixtures;
use App\Repository\UserRepository;

class RegistrationControllerTest extends WebTestCase
{
    private $entityManager;
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // Créez le client avant d'accéder à l'EntityManager
        $this->client = static::createClient();
        $container = $this->client->getContainer();

        // Initialisez l'EntityManager
        $this->entityManager = $container->get('doctrine')->getManager();

        $loader = new Loader();
        $loader->addFixture(new AppFixtures($container->get('security.password_hasher')));
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->purge();
        $executor->execute($loader->getFixtures());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Close the entity manager
        if ($this->entityManager) {
            $this->entityManager->close();
            $this->entityManager = null; // éviter les memory leaks
        }

        // Reset the kernel to ensure no exceptions are left
        self::ensureKernelShutdown();
    }

    public function testRegister(): void
    {
        // Générer une adresse email unique pour chaque test
        $uniqueEmail = 'test' . uniqid() . '@example.com';

        try {
            $crawler = $this->client->request('GET', '/register');

            $this->assertResponseIsSuccessful();
            $this->assertSelectorTextContains('h1', 'Register');

            $form = $crawler->selectButton('Register')->form([
                'registration_form[email]' => $uniqueEmail,
                'registration_form[nom]' => 'Doe',
                'registration_form[prenom]' => 'John',
                'registration_form[plainPassword]' => 'ValidPassword123!',
                'registration_form[agreeTerms]' => true,
                'registration_form[captcha]' => 'mockCaptchaResponse', // Simulé ou désactivé dans le test
            ]);

            $this->client->submit($form);

            $this->assertResponseRedirects('/');

            // Follow the redirect
            $this->client->followRedirect();

            // Vérifiez la présence du titre sur la page d'accueil
            $this->assertSelectorTextContains('h1.title', 'Fleurs,🌻 ce dont le monde a besoin');

            // Vérifiez que l'utilisateur a été créé
            $userRepository = self::getContainer()->get(UserRepository::class);
            $user = $userRepository->findOneByEmail($uniqueEmail);

            $this->assertNotNull($user);

            // Ajoutez une assertion pour vérifier que le formulaire a été soumis sans erreurs
            $this->assertSelectorNotExists('.form-error-message');
        } catch (\Throwable $e) {
            // Si une exception est levée, la gestion est effectuée ici
            $this->fail('Exception during test: ' . $e->getMessage());
        }
    }
}
