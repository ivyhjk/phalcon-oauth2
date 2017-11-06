<?php

require dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

use League\OAuth2\Server\CryptKey;
use Phalcon\Db\Adapter\Pdo\Mysql;
use League\OAuth2\Server\Exception\OAuthServerException;
use Ivyhjk\Phalcon\OAuth2\Server\AuthorizationServer;
use Ivyhjk\Phalcon\OAuth2\Server\Grant\PasswordGrant;
use Ivyhjk\Phalcon\OAuth2\Server\Repository\AccessToken as AccessTokenRepository;
use Ivyhjk\Phalcon\OAuth2\Server\Repository\Client as ClientRepository;
use Ivyhjk\Phalcon\OAuth2\Server\Repository\RefreshToken as RefreshTokenRepository;
use Ivyhjk\Phalcon\OAuth2\Server\Repository\Scope as ScopeRepository;
use Ivyhjk\Phalcon\OAuth2\Server\Repository\User as UserRepository;

// Pretty URLs.
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$_GET['_url'] = $_SERVER['REQUEST_URI'];

$di = new \Phalcon\DI\FactoryDefault();
$app = new \Phalcon\Mvc\Micro($di);

$app->setService('db', function () {
    return new Mysql([
        'host' => 'development.rootbin.sh',
        'username' => 'oauth2',
        'password' => 'tn2fopC7eZy374fV',
        'dbname' => 'oauth2',
    ]);
});

// Authorization server service.
$app->setService('oauth2-autorization-server', function () {
    // Setup the authorization server
    $server = new AuthorizationServer(
        new ClientRepository(), // instance of ClientRepositoryInterface
        new AccessTokenRepository(), // instance of AccessTokenRepositoryInterface
        new ScopeRepository(), // instance of ScopeRepositoryInterface
        new CryptKey('file://' . __DIR__ . '/../private.key', null, false), // path to private key
        'sdppVNBLNkDvCLgLhFzGNwF65XbzbOFwifkqEfp+FYo=' // encryption key
    );

    return $server;
}, true);

$app->notFound(function () use ($app) {
    $response = $app->getService('response');
    $response->setStatusCode(404, 'Not Found');
    $response->setJsonContent([
        'error' => 'not found'
    ]);

    return $response;
});

// Access token example.
$app->post('/access_token', function () {
    $server = $this->getService('oauth2-autorization-server');

    $grant = new PasswordGrant(
        new UserRepository(),
        new RefreshTokenRepository()
    );

    // refresh tokens will expire after 1 month
    $grant->setRefreshTokenTTL(new \DateInterval('P1M'));

    // Enable the password grant on the server with a token TTL of 1 hour
    $server->enableGrantType(
        $grant,
        new \DateInterval('PT1H') // access tokens will expire after 1 hour
    );

    try {
        $response = $server->respondToPhalconAccessTokenRequest(
            $this->getService('request'),
            $this->getService('response')
        );
    } catch (OAuthServerException $e) {
        $response = $this->getService('response');
        $response->setStatusCode($e->getHttpStatusCode());
        $response->setJsonContent([
            'error' => $e->getErrorType(),
            'message' => $e->getMessage()
        ]);
    } catch (\Exception $e) {
        $response = $this->getService('response');
        $response->setStatusCode(500);
        $response->setJsonContent([
            'error' => $e->getMessage()
        ]);
    }

    return $response;
});

$app->handle();
