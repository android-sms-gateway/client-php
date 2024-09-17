<?php

namespace AndroidSmsGateway\Exceptions;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class HttpException extends \RuntimeException implements RequestExceptionInterface {
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param string $message
     */
    public function __construct(
        $message,
        RequestInterface $request,
        ResponseInterface $response,
        \Exception $previous = null
    ) {
        parent::__construct($message, 0, $previous);

        $this->request = $request;
        $this->response = $response;

        $this->code = $response->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(): RequestInterface {
        return $this->request;
    }

    /**
     * Returns the response.
     *
     * @return ResponseInterface
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Factory method to create a new exception with a normalized error message.
     */
    public static function create(
        RequestInterface $request,
        ResponseInterface $response,
        \Exception $previous = null
    ): self {
        $message = sprintf(
            '[url] %s [http method] %s [status code] %s [reason phrase] %s',
            $request->getRequestTarget(),
            $request->getMethod(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        return new static($message, $request, $response, $previous);
    }
}