<?php
// tests/Mock/MockRecaptchaValidator.php
namespace App\Tests\Mock;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MockRecaptchaValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        // Ne faites rien, simulez toujours une validation réussie
    }
}
