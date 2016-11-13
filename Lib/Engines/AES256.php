<?php
/**
 * @author   Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright: (c) 2016 Beeflow Ltd
 *
 * Date: 25.09.16 14:46
 */

namespace Beeflow\BeeflowCryptBundle\Lib\Engines;

use Beeflow\BeeflowCryptBundle\Lib\Engine;
use Beeflow\BeeflowCryptBundle\Lib\Interfaces\EngineInterface;

class AES256 extends Engine implements EngineInterface
{
    const METHOD = 'aes-256-cbc';

    private $cert;

    /**
     * @param string $message
     *
     * @return string
     * @throws \Exception
     */
    public function encrypt($message)
    {
        if (mb_strlen($this->cert, '8bit') !== 32) {
            throw new \Exception("Needs a 256-bit key!");
        }
        $ivsize = openssl_cipher_iv_length(self::METHOD);
        $iv = openssl_random_pseudo_bytes($ivsize);

        $ciphertext = openssl_encrypt(
            $message,
            self::METHOD,
            $this->cert,
            OPENSSL_RAW_DATA,
            $iv
        );

        return \base64_encode($iv . $ciphertext);
    }

    /**
     * @param string $message
     *
     * @return string
     * @throws \Exception
     */
    public function decrypt($message)
    {
        $message = base64_decode($message);
        if (mb_strlen($this->cert, '8bit') !== 32) {
            throw new \Exception("Needs a 256-bit key! " . $this->cert);
        }
        $ivsize = openssl_cipher_iv_length(self::METHOD);
        $iv = mb_substr($message, 0, $ivsize, '8bit');
        $ciphertext = mb_substr($message, $ivsize, null, '8bit');

        return openssl_decrypt(
            $ciphertext,
            self::METHOD,
            $this->cert,
            OPENSSL_RAW_DATA,
            $iv
        );
    }

    /**
     *
     */
    public function prepareCerts()
    {
        if (!file_exists($this->certDir . $this->certFile)) {
            return;
        }
        $this->cert = file_get_contents($this->certDir . $this->certFile);
    }
}