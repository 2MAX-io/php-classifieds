<?php

namespace App\Security\Encoder;

use Hautelook\Phpass\PasswordHash;
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;
use Symfony\Component\Security\Core\Encoder\SelfSaltingEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class PhpassPasswordEncoder extends BasePasswordEncoder implements SelfSaltingEncoderInterface
{
    /**
     * {@inheritdoc}
     */
    public function encodePassword($raw, $salt)
    {
        if ($this->isPasswordTooLong($raw)) {
            throw new BadCredentialsException('Invalid password.');
        }

        return $this->getEncoder()->HashPassword($raw);
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        return !$this->isPasswordTooLong($raw) && $this->getEncoder()->CheckPassword($raw, $encoded);
    }

    private function getEncoder(): PasswordHash
    {
        return new PasswordHash(8,false);
    }
}
