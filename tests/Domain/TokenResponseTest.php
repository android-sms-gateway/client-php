<?php

namespace AndroidSmsGateway\Tests\Domain;

use AndroidSmsGateway\Domain\TokenResponse;
use PHPUnit\Framework\TestCase;

class TokenResponseTest extends TestCase {
    public function testTokenResponseCreation(): void {
        $accessToken = 'test-access-token';
        $tokenType = 'Bearer';
        $id = 'token-id';
        $expiresAt = '2023-12-31T23:59:59Z';

        $tokenResponse = new TokenResponse($accessToken, $tokenType, $id, $expiresAt);

        $this->assertEquals($accessToken, $tokenResponse->AccessToken());
        $this->assertEquals($tokenType, $tokenResponse->TokenType());
        $this->assertEquals($id, $tokenResponse->ID());
        $this->assertEquals($expiresAt, $tokenResponse->ExpiresAt());
    }

    public function testTokenResponseSetters(): void {
        $tokenResponse = new TokenResponse('initial-token', 'Bearer', 'initial-id', '2023-01-01T00:00:00Z');

        $newAccessToken = 'new-access-token';
        $newTokenType = 'JWT';
        $newId = 'new-token-id';
        $newExpiresAt = '2024-12-31T23:59:59Z';

        $tokenResponse->setAccessToken($newAccessToken);
        $tokenResponse->setTokenType($newTokenType);
        $tokenResponse->setId($newId);
        $tokenResponse->setExpiresAt($newExpiresAt);

        $this->assertEquals($newAccessToken, $tokenResponse->AccessToken());
        $this->assertEquals($newTokenType, $tokenResponse->TokenType());
        $this->assertEquals($newId, $tokenResponse->ID());
        $this->assertEquals($newExpiresAt, $tokenResponse->ExpiresAt());
    }

    public function testTokenResponseToObject(): void {
        $accessToken = 'test-access-token';
        $tokenType = 'Bearer';
        $id = 'token-id';
        $expiresAt = '2023-12-31T23:59:59Z';

        $tokenResponse = new TokenResponse($accessToken, $tokenType, $id, $expiresAt);
        $obj = $tokenResponse->toObject();

        $this->assertEquals($accessToken, $obj->access_token);
        $this->assertEquals($tokenType, $obj->token_type);
        $this->assertEquals($id, $obj->id);
        $this->assertEquals($expiresAt, $obj->expires_at);
    }

    public function testTokenResponseFromObject(): void {
        $obj = new \stdClass();
        $obj->access_token = 'test-access-token';
        $obj->token_type = 'Bearer';
        $obj->id = 'token-id';
        $obj->expires_at = '2023-12-31T23:59:59Z';

        $tokenResponse = TokenResponse::FromObject($obj);

        $this->assertEquals($obj->access_token, $tokenResponse->AccessToken());
        $this->assertEquals($obj->token_type, $tokenResponse->TokenType());
        $this->assertEquals($obj->id, $tokenResponse->ID());
        $this->assertEquals($obj->expires_at, $tokenResponse->ExpiresAt());
    }

    public function testTokenResponseFromObjectWithDefaultValues(): void {
        $obj = new \stdClass();

        $tokenResponse = TokenResponse::FromObject($obj);

        $this->assertEquals('', $tokenResponse->AccessToken());
        $this->assertEquals('', $tokenResponse->TokenType());
        $this->assertEquals('', $tokenResponse->ID());
        $this->assertEquals('', $tokenResponse->ExpiresAt());
    }
}