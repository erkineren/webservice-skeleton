<?php

namespace App\Response;


use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;

/**
 * Class ApiResponse
 * @package App\Response
 */
class ApiResponse extends Response implements ResponseInterface
{

    /**
     * @param $data
     * @param string $successMessage
     * @param string $errorMessage
     * @param null $status
     * @param int $encodingOptions
     * @return Response
     */
    public function withJsonAuto($data, $successMessage = '', $errorMessage = '', $status = null, $encodingOptions = 0)
    {
        if ($data)
            return $this->withJsonSuccess($data, $successMessage, $status, $encodingOptions);
        else
            return $this->withJsonError($errorMessage, $status, $encodingOptions);
    }

    /**
     * @param $data
     * @param string $message
     * @param null $status
     * @param int $encodingOptions
     * @return Response
     */
    public function withJsonSuccess($data, $message = '', $status = null, $encodingOptions = 0)
    {
        $data = [
            'head' => [
                'status' => 'success',
                'message' => $message,
                'count' => is_array($data) ? count($data) : ''
            ],
            'body' => $data
        ];
        return parent::withJson($data, $status, $encodingOptions);
    }

    /**
     * @param $message
     * @param null $status
     * @param int $encodingOptions
     * @return Response
     */
    public function withJsonError($message, $status = null, $encodingOptions = 0)
    {
        $data = [
            'head' => [
                'status' => 'failure',
                'message' => $message,
            ],
            'body' => []
        ];
        return parent::withJson($data, $status, $encodingOptions);
    }

}






















