<?php

namespace App\Tests\Security;

use App\Security\AppAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Mockery;

class AppAuthenticatorTest extends TestCase
{
    public function testAuthenticate()
    {
        // Arrange
        $urlGenerator = Mockery::mock(UrlGeneratorInterface::class);
        $authenticator = new AppAuthenticator($urlGenerator);

        $request = Mockery::mock(Request::class);
        $session = Mockery::mock(SessionInterface::class);

        $inputBag = new InputBag([
            'email' => 'test@example.com',
            'password' => 'password',
            '_csrf_token' => 'csrf_token',
        ]);

        $request->shouldReceive('getPayload')->andReturn($inputBag);
        $request->shouldReceive('getSession')->andReturn($session);

        $session->shouldReceive('set')
            ->with(SecurityRequestAttributes::LAST_USERNAME, 'test@example.com');

        // Act
        $passport = $authenticator->authenticate($request);

        // Assert
        $this->assertInstanceOf(Passport::class, $passport);
        $this->assertInstanceOf(UserBadge::class, $passport->getBadge(UserBadge::class));
        $this->assertInstanceOf(CsrfTokenBadge::class, $passport->getBadge(CsrfTokenBadge::class));
        
        // No direct assertion on PasswordCredentials, since it's embedded in the Passport's creation.
    }

    public function testOnAuthenticationSuccessWithTargetPath()
    {
        // Arrange
        $urlGenerator = Mockery::mock(UrlGeneratorInterface::class);
        $authenticator = new AppAuthenticator($urlGenerator);

        $request = Mockery::mock(Request::class);
        $session = Mockery::mock(SessionInterface::class);
        $token = Mockery::mock(TokenInterface::class);

        $request->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('get')->with('_security.main.target_path')->andReturn('some_target_path');

        // Act
        $response = $authenticator->onAuthenticationSuccess($request, $token, 'main');

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('some_target_path', $response->getTargetUrl());
    }

    public function testOnAuthenticationSuccessWithoutTargetPath()
    {
        // Arrange
        $urlGenerator = Mockery::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')
            ->with('app_home')
            ->andReturn('/home');

        $authenticator = new AppAuthenticator($urlGenerator);

        $request = Mockery::mock(Request::class);
        $session = Mockery::mock(SessionInterface::class);
        $token = Mockery::mock(TokenInterface::class);

        $request->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('get')->with('_security.main.target_path')->andReturn(null);

        // Act
        $response = $authenticator->onAuthenticationSuccess($request, $token, 'main');

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/home', $response->getTargetUrl());
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
