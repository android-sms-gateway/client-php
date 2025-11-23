<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Domain\TokenRequest;
use PHPUnit\Framework\TestCase;

class TokenRequestTest extends TestCase {
    public function testTokenRequestCreation(): void {
        $scopes = ['read', 'write'];
        $ttl = 3600;

        $tokenRequest = new TokenRequest($scopes, $ttl);

        $this->assertEquals($scopes, $tokenRequest->Scopes());
        $this->assertEquals($ttl, $tokenRequest->TTL());
    }

    public function testTokenRequestCreationWithoutTtl(): void {
        $scopes = ['read'];

        $tokenRequest = new TokenRequest($scopes);

        $this->assertEquals($scopes, $tokenRequest->Scopes());
        $this->assertNull($tokenRequest->TTL());
    }

    public function testTokenRequestSetters(): void {
        $tokenRequest = new TokenRequest(['read']);

        $newScopes = ['read', 'write', 'admin'];
        $newTtl = 7200;

        $tokenRequest->setScopes($newScopes);
        $tokenRequest->setTtl($newTtl);

        $this->assertEquals($newScopes, $tokenRequest->Scopes());
        $this->assertEquals($newTtl, $tokenRequest->TTL());
    }

    public function testTokenRequestToObject(): void {
        $scopes = ['read', 'write'];
        $ttl = 3600;

        $tokenRequest = new TokenRequest($scopes, $ttl);
        $obj = $tokenRequest->toObject();

        $this->assertEquals($scopes, $obj->scopes);
        $this->assertEquals($ttl, $obj->ttl);
    }

    public function testTokenRequestToObjectWithoutTtl(): void {
        $scopes = ['read'];

        $tokenRequest = new TokenRequest($scopes);
        $obj = $tokenRequest->toObject();

        $this->assertEquals($scopes, $obj->scopes);
        $this->assertObjectNotHasProperty('ttl', $obj);
    }

    public function testTokenRequestFromObject(): void {
        $obj = new \stdClass();
        $obj->scopes = ['read', 'write'];
        $obj->ttl = 3600;

        $tokenRequest = TokenRequest::FromObject($obj);

        $this->assertEquals($obj->scopes, $tokenRequest->Scopes());
        $this->assertEquals($obj->ttl, $tokenRequest->TTL());
    }

    public function testTokenRequestFromObjectWithoutTtl(): void {
        $obj = new \stdClass();
        $obj->scopes = ['read'];

        $tokenRequest = TokenRequest::FromObject($obj);

        $this->assertEquals($obj->scopes, $tokenRequest->Scopes());
        $this->assertNull($tokenRequest->TTL());
    }

    public function testTokenRequestFromObjectWithDefaultValues(): void {
        $obj = new \stdClass();

        $tokenRequest = TokenRequest::FromObject($obj);

        $this->assertEquals([], $tokenRequest->Scopes());
        $this->assertNull($tokenRequest->TTL());
    }
}