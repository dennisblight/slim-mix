<?php
namespace Core\Middleware\Logger\Writer;

use Laminas\Diactoros\Request;

class DatabaseWriter//  extends BaseWriter
{
    // public function logRequest(Request $request)
    // {
    //     $body = $request->getBody();
    //     $contentType = $request->getContentType();
    //     $maxLength = $this->getMaxLength();
    //     if(empty($contentType))
    //     {
    //         $contentType = 'application/octet-stream';
    //     }

    //     if($body->getSize() > $maxLength)
    //     {
    //         $body = '[' . $contentType . ':' . $body->getSize() . 'B]';
    //     }

    //     $body = (string) $body;
    //     if(is_binary($body))
    //     {
    //         $body = '[' . $contentType . ':' . strlen($body) . 'B]';
    //     }

    //     $paramsHeader = substr(json_encode($request->getHeaders()), 0, $maxLength);

    //     $paramsGet = substr(json_encode($_GET), 0, $maxLength);

    //     $paramsPost = substr(json_encode($_POST), 0, $maxLength);

    //     $paramsFile = substr(json_encode($_FILES), 0, $maxLength);

    //     return DB::connection(array_item($this->config, 'connection', 'main'))
    //         ->table(array_item($this->config, 'request_table', 'log_request'))
    //         ->insert([
    //             'created_at'  => timestamp(),
    //             'url'         => (string) $request->getUri(),
    //             'method'      => $request->getMethod(),
    //             'headers'     => $paramsHeader,
    //             'body'        => $body,
    //             'params_get'  => $paramsGet,
    //             'params_post' => $paramsPost,
    //             'params_file' => $paramsFile,
    //         ])
    //     ;
    // }

    // public function logResponse($requestResult, $response)
    // {
    //     $body = $response->getBody();
    //     $contentType = $response->getHeader('content-type');
    //     $maxLength = $this->getMaxLength();
        
    //     if(is_array($contentType))
    //     {
    //         $contentType = $contentType[0];
    //     }

    //     if(empty($contentType))
    //     {
    //         $contentType = 'application/octet-stream';
    //     }

    //     if($body->getSize() > $maxLength)
    //     {
    //         $body = '[' . $contentType . ':' . $body->getSize() . 'B]';
    //     }

    //     $body = (string) $body;
    //     if(is_binary($body))
    //     {
    //         $body = '[' . $contentType . ':' . strlen($body) . 'B]';
    //     }

    //     $paramsHeader = substr(json_encode($response->getHeaders()), 0, $maxLength);
    //     Logger::debug("Request Finished");

    //     return DB::connection(array_item($this->config, 'connection', 'main'))
    //         ->table(array_item($this->config, 'response_table', 'log_response'))
    //         ->insert([
    //             'request_id'     => $requestResult,
    //             'created_at'     => date('Y-m-d H:i:s'),
    //             'status_code'    => $response->getStatusCode(),
    //             'status_message' => $response->getReasonPhrase(),
    //             'headers'        => $paramsHeader,
    //             'body'           => $body,
    //         ])
    //     ;
    // }
}