<?php
/**
 * @author   Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright: (c) 2016 Beeflow Ltd
 *
 * Date: 25.09.16 14:46
 */

namespace Beeflow\BeeflowCryptBundle\Lib\Engines;


use Beeflow\BeeflowCryptBundle\Lib\Interfaces\Engine;

class AES256 implements Engine
{

    const METHOD = 'aes-256-cbc';

    /**
     * @param string $message
     * @param string $key
     *
     * @return string
     * @throws \Exception
     */
    public function encrypt($message, $key)
    {
        if (mb_strlen($key, '8bit') !== 32) {
            throw new Exception("Needs a 256-bit key!");
        }
        $ivsize = openssl_cipher_iv_length(self::METHOD);
        $iv = openssl_random_pseudo_bytes($ivsize);

        $ciphertext = openssl_encrypt(
            $message,
            self::METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return base64_encode($iv . $ciphertext);
    }

    /**
     * @param string $message
     * @param string $key
     *
     * @return string
     * @throws \Exception
     */
    public function decrypt($message, $key)
    {
        $message = base64_decode($message);
        if (mb_strlen($key, '8bit') !== 32) {
            throw new \Exception("Needs a 256-bit key!");
        }
        $ivsize = openssl_cipher_iv_length(self::METHOD);
        $iv = \mb_substr($message, 0, $ivsize, '8bit');
        $ciphertext = mb_substr($message, $ivsize, null, '8bit');

        return openssl_decrypt(
            $ciphertext,
            self::METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
    }
}