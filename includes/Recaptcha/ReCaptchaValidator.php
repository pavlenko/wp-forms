<?php
/**
 * SunNY Creative Technologies
 *
 *   #####                                ##     ##    ##      ##
 * ##     ##                              ###    ##    ##      ##
 * ##                                     ####   ##     ##    ##
 * ##           ##     ##    ## #####     ## ##  ##      ##  ##
 *   #####      ##     ##    ###    ##    ##  ## ##       ####
 *        ##    ##     ##    ##     ##    ##   ####        ##
 *        ##    ##     ##    ##     ##    ##    ###        ##
 * ##     ##    ##     ##    ##     ##    ##     ##        ##
 *   #####        #######    ##     ##    ##     ##        ##
 *
 * C  R  E  A  T  I  V  E     T  E  C  H  N  O  L  O  G  I  E  S
 */

namespace PE\WP\Forms\Recaptcha;

use ReCaptcha\ReCaptcha;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ReCaptchaValidator extends ConstraintValidator
{
    /*private $revisor;

    public function __construct($revisor)
    {
        $this->revisor = $revisor;
    }*/

    /**
     * @inheritdoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof ReCaptchaConstraint)) {
            throw new UnexpectedTypeException($constraint, ReCaptchaConstraint::class);
        }

        if (null === $value || '' === $value) {
            $this->context->buildViolation($constraint->responseMissingMessage)->addViolation();
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $recaptcha = new ReCaptcha($constraint->secretKey);
        $response  = $recaptcha->verify((string) $value);

        if (!$response->isSuccess()) {
            foreach ($response->getErrorCodes() as $errorCode) {
                $message = ReCaptchaConstraint::translateUpstreamResponse($errorCode);
                $this->context->buildViolation($constraint->{$message})->addViolation();
            }
        }
    }
}