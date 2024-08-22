<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Order; // Assurez-vous que cette ligne est présente
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
        $this->user->setEmail('test@example.com');
        $this->user->setFirstName('John');
        $this->user->setLastName('Doe');
        $this->user->setPassword('hashed_password');
        $this->user->setCreatedAt(new DateTimeImmutable());
    }

    public function testInitialValues()
    {
        $this->assertNull($this->user->getId());
        $this->assertEquals('test@example.com', $this->user->getEmail());
        $this->assertEquals('John', $this->user->getFirstName());
        $this->assertEquals('Doe', $this->user->getLastName());
        $this->assertInstanceOf(DateTimeImmutable::class, $this->user->getCreatedAt());
    }

    public function testSettersAndGetters()
    {
        $this->user->setEmail('newemail@example.com');
        $this->user->setFirstName('Jane');
        $this->user->setLastName('Smith');
        $this->user->setPassword('new_hashed_password');
        $this->user->setCreatedAt(new DateTimeImmutable('2023-01-01'));

        $this->assertEquals('newemail@example.com', $this->user->getEmail());
        $this->assertEquals('Jane', $this->user->getFirstName());
        $this->assertEquals('Smith', $this->user->getLastName());
        $this->assertEquals('new_hashed_password', $this->user->getPassword());
        $this->assertEquals(new DateTimeImmutable('2023-01-01'), $this->user->getCreatedAt());
    }

    public function testGetRoles()
    {
        $this->assertEquals(['ROLE_USER'], $this->user->getRoles());

        $this->user->setRoles(['ROLE_ADMIN']);
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $this->user->getRoles());
    }

    /**
     * @return MockObject|Order
     */
    private function createOrderMock(): MockObject
    {
        return $this->createMock(Order::class);
    }

    public function testOrdersManagement()
    {
        $order1 = $this->createOrderMock();
        $order2 = $this->createOrderMock();

        $this->assertInstanceOf(Collection::class, $this->user->getOrders());
        $this->assertCount(0, $this->user->getOrders());

        $this->user->addOrder($order1);
        $this->assertCount(1, $this->user->getOrders());

        $this->user->addOrder($order2);
        $this->assertCount(2, $this->user->getOrders());

        $this->user->removeOrder($order1);
        $this->assertCount(1, $this->user->getOrders());

        $this->user->removeOrder($order2);
        $this->assertCount(0, $this->user->getOrders());
    }

    public function testUserIdentifier()
    {
        $this->assertEquals('test@example.com', $this->user->getUserIdentifier());
    }

    public function testEraseCredentials()
    {
        // No sensitive data to clear in this test, just ensure no exceptions are thrown
        $this->user->eraseCredentials();
        $this->assertTrue(true); // Simply to pass the test
    }
}
