<?php

namespace AndroidSmsGateway\Domain;

use AndroidSmsGateway\Interfaces\SerializableInterface;

class TokenRequest implements SerializableInterface {
    /** @var string[] */
    private array $scopes;
    private ?int $ttl;

    /**
     * @param string[] $scopes
     * @param int|null $ttl
     */
    public function __construct(array $scopes, ?int $ttl = null) {
        $this->scopes = $scopes;
        $this->ttl = $ttl;
    }

    /**
     * @return string[]
     */
    public function Scopes(): array {
        return $this->scopes;
    }

    /**
     * @param string[] $scopes
     * @return self
     */
    public function setScopes(array $scopes): self {
        $this->scopes = $scopes;
        return $this;
    }

    public function TTL(): ?int {
        return $this->ttl;
    }

    public function setTtl(?int $ttl): self {
        $this->ttl = $ttl;
        return $this;
    }

    public function toObject(): \stdClass {
        $obj = new \stdClass();
        $obj->scopes = $this->scopes;

        if ($this->ttl !== null) {
            $obj->ttl = $this->ttl;
        }

        return $obj;
    }

    /**
     * @param object $obj
     * @return self
     */
    public static function FromObject(object $obj): self {
        return new self(
            $obj->scopes ?? [],
            $obj->ttl ?? null
        );
    }
}