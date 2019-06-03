<?php

declare(strict_types=1);

namespace App\Helper;

class SerializerHelper
{
    /**
     * @see \Symfony\Component\Security\Http\Firewall\ContextListener::safelyUnserialize
     *
     * @return mixed
     */
    public static function safelyUnserialize(string $serializedToken)
    {
        $e = $token = null;
        $prevUnserializeHandler = ini_set('unserialize_callback_func', __CLASS__.'::handleUnserializeCallback');
        $prevErrorHandler = set_error_handler(
            static function ($type, $msg, $file, $line, $context = []) use (&$prevErrorHandler) {
            if (__FILE__ === $file) {
                throw new \ErrorException($msg, 0x37313bc, $type, $file, $line);
            }

            return $prevErrorHandler ? $prevErrorHandler($type, $msg, $file, $line, $context) : false;
        });

        try {
            /** @noinspection UnserializeExploitsInspection */
            $token = \unserialize($serializedToken);
        } catch (\Throwable $e) {
        }
        restore_error_handler();
        ini_set('unserialize_callback_func', $prevUnserializeHandler);
        if ($e) {
            if (!$e instanceof \ErrorException || 0x37313bc !== $e->getCode()) {
                throw $e;
            }
        }

        return $token;
    }

    /**
     * @param string|mixed $class
     *
     * @internal
     */
    public static function handleUnserializeCallback($class): void
    {
        throw new \UnexpectedValueException('Class not found: ' . $class, 0x37313bc);
    }
}
