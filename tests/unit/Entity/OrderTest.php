<?php

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\ProductLine;
use App\Enum\OrderStatus;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class OrderTest extends TestCase
{
    private Order $order;

    protected function setUp(): void
    {
        $this->order = new Order();
        $this->order->setCreationDate(new DateTimeImmutable());
        $this->order->setTotalPrice(100.00);
        $this->order->setStatus(OrderStatus::PENDING);
        $this->order->setPickUpDate(new DateTimeImmutable());
        $this->order->setMessage('Test message');
    }

    public function testInitialValues()
    {
        $this->assertNull($this->order->getId());
        $this->assertInstanceOf(\DateTimeInterface::class, $this->order->getCreationDate());
        $this->assertEquals(100.00, $this->order->getTotalPrice());
        $this->assertEquals(OrderStatus::PENDING, $this->order->getStatus());
        $this->assertInstanceOf(\DateTimeInterface::class, $this->order->getPickUpDate());
        $this->assertEquals('Test message', $this->order->getMessage());
    }

    public function testSettersAndGetters()
    {
        $creationDate = new DateTimeImmutable('2024-01-01');
        $paymentDate = new DateTimeImmutable('2024-02-01');
        $pickUpDate = new DateTimeImmutable('2024-03-01');
        
        $this->order->setCreationDate($creationDate);
        $this->order->setPaymentDate($paymentDate);
        $this->order->setPickUpDate($pickUpDate);
        $this->order->setTotalPrice(200.00);
        $this->order->setStatus(OrderStatus::COMPLETED);
        $this->order->setMessage('Updated message');
        
        $this->assertEquals($creationDate, $this->order->getCreationDate());
        $this->assertEquals($paymentDate, $this->order->getPaymentDate());
        $this->assertEquals($pickUpDate, $this->order->getPickUpDate());
        $this->assertEquals(200.00, $this->order->getTotalPrice());
        $this->assertEquals(OrderStatus::COMPLETED, $this->order->getStatus());
        $this->assertEquals('Updated message', $this->order->getMessage());
    }

    public function testUserAssociation()
    {
        $user = new User();
        $this->order->setUser($user);
        $this->assertSame($user, $this->order->getUser());
    }

    public function testProductLinesManagement()
    {
        $productLine1 = $this->createMock(ProductLine::class);
        $productLine2 = $this->createMock(ProductLine::class);

        $this->assertInstanceOf(Collection::class, $this->order->getProductLines());
        $this->assertCount(0, $this->order->getProductLines());

        $this->order->addProductLine($productLine1);
        $this->assertCount(1, $this->order->getProductLines());

        $this->order->addProductLine($productLine2);
        $this->assertCount(2, $this->order->getProductLines());

        $this->order->removeProductLine($productLine1);
        $this->assertCount(1, $this->order->getProductLines());

        $this->order->removeProductLine($productLine2);
        $this->assertCount(0, $this->order->getProductLines());
    }
}
