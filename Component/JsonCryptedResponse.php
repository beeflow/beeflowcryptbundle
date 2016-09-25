<?php
/**
 * @author   Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright: (c) 2016 Beeflow Ltd
 *
 * Date: 25.09.16 15:53
 */

namespace Beeflow\BeeflowCryptBundle\Component;


use Beeflow\BeeflowCryptBundle\Lib\BFCrypt;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonCryptedResponse extends JsonResponse
{

    /**
     * JsonCryptedResponse constructor.
     *
     * @param array $data ['crypt_method' => 'AES256', 'key' => '256-bit key', 'data' => Mixed]
     * @param int   $status
     * @param array $headers
     */
    public function __construct(array $data, $status = 200, array $headers = [])
    {
        $crypt = new BFCrypt($data['crypt_method']);
        if (is_array($data['data'])) {
            $myData = json_encode($data['data']);
        } else {
            $myData = $data['data'];
        }

        $response = [
            'crypted_response' => $crypt->encrypt($myData, $data['key'])
        ];

        parent::__construct($response, $status, $headers);
    }
}