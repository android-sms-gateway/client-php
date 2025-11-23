<?php

namespace AndroidSmsGateway\Domain;

use AndroidSmsGateway\Interfaces\SerializableInterface;

class TokenResponse implements SerializableInterface {
    private string $accessToken;
    private string $tokenType;
    private string $id;
    private string $expiresAt;

    public function __construct(
        string $accessToken,
        string $tokenType,
        string $id,
        string $expiresAt
    ) {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->id = $id;
        $this->expiresAt = $expiresAt;
    }

    public function AccessToken(): string {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function TokenType(): string {
        return $this->tokenType;
    }

    public function setTokenType(string $tokenType): self {
        $this->tokenType = $tokenType;
        return $this;
    }

    public function ID(): string {
        return $this->id;
    }

    public function setId(string $id): self {
        $this->id = $id;
        return $this;
    }

    public function ExpiresAt(): string {
        return $this->expiresAt;
    }

    public function setExpiresAt(string $expiresAt): self {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function toObject(): \stdClass {
        $obj = new \stdClass();
        $obj->access_token = $this->accessToken;
        $obj->token_type = $this->tokenType;
        $obj->id = $this->id;
        $obj->expires_at = $this->expiresAt;

        return $obj;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        return new self(
            $obj->access_token ?? '',
            $obj->token_type ?? '',
            $obj->id ?? '',
            $obj->expires_at ?? ''
        );
    }
}