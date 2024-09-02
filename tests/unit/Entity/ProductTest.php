<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use App\Entity\ProductLine;
use App\Entity\Category;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;

class ProductTest extends TestCase
{
    private Product $product;

    protected function setUp(): void
    {
        $this->product = new Product();
        $this->product->setName('Test Product');
        $this->product->setStripeId('stripe_123');
        $this->product->setPrice(99.99);
        $this->product->setStock(10);
        $this->product->setColor('Red');
        $this->product->setSize('M');
        $this->product->setWeight(1.5);
        $this->product->setDescription('A test product.');
        $this->product->setImage('image.jpg');
    }

    public function testInitialValues()
    {
        $this->assertNull($this->product->getId());
        $this->assertEquals('Test Product', $this->product->getName());
        $this->assertEquals('stripe_123', $this->product->getStripeId());
        $this->assertEquals(99.99, $this->product->getPrice());
        $this->assertEquals(10, $this->product->getStock());
        $this->assertEquals('Red', $this->product->getColor());
        $this->assertEquals('M', $this->product->getSize());
        $this->assertEquals(1.5, $this->product->getWeight());
        $this->assertEquals('A test product.', $this->product->getDescription());
        $this->assertEquals('image.jpg', $this->product->getImage());
    }

    public function testSettersAndGetters()
    {
        $this->product->setName('New Name');
        $this->product->setStripeId('stripe_456');
        $this->product->setPrice(79.99);
        $this->product->setStock(20);
        $this->product->setColor('Blue');
        $this->product->setSize('L');
        $this->product->setWeight(2.0);
        $this->product->setDescription('Updated product description.');
        $this->product->setImage('new_image.jpg');

        $this->assertEquals('New Name', $this->product->getName());
        $this->assertEquals('stripe_456', $this->product->getStripeId());
        $this->assertEquals(79.99, $this->product->getPrice());
        $this->assertEquals(20, $this->product->getStock());
        $this->assertEquals('Blue', $this->product->getColor());
        $this->assertEquals('L', $this->product->getSize());
        $this->assertEquals(2.0, $this->product->getWeight());
        $this->assertEquals('Updated product description.', $this->product->getDescription());
        $this->assertEquals('new_image.jpg', $this->product->getImage());
    }

    public function testProductLinesManagement()
    {
        $productLine1 = $this->createMock(ProductLine::class);
        $productLine2 = $this->createMock(ProductLine::class);

        $this->assertInstanceOf(Collection::class, $this->product->getProductLines());
        $this->assertCount(0, $this->product->getProductLines());

        $this->product->addProductLine($productLine1);
        $this->assertCount(1, $this->product->getProductLines());

        $this->product->addProductLine($productLine2);
        $this->assertCount(2, $this->product->getProductLines());

        $this->product->removeProductLine($productLine1);
        $this->assertCount(1, $this->product->getProductLines());

        $this->product->removeProductLine($productLine2);
        $this->assertCount(0, $this->product->getProductLines());
    }

    public function testCategoryAssociation()
    {
        $category = new Category();
        $this->product->setCategory($category);
        $this->assertSame($category, $this->product->getCategory());

        $this->product->setCategory(null);
        $this->assertNull($this->product->getCategory());
    }

    public function testEmptyDescription()
    {
        $this->product->setDescription(''); // Pass an empty string instead of null
        $this->assertEquals('', $this->product->getDescription());
    }
}
