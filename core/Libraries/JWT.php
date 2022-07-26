<?php
namespace Core\Libraries;

use Core\Collection;
use DI\Container;
use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use InvalidArgumentException;
use Laminas\Diactoros\Request;
use UnexpectedValueException;

class JWT
{
    /** @var Collection */
    private $config;

    public function __construct(Container $container)
    {
        $this->request = $container->get(Request::class);
        $this->config = $container->get('config.jwt');
    }

    public function encode($payload)
    {
        $config = $this->getConfig();
        $key = $config->get('private_key', $config->get('key'));
        if(is_callable($key))
        {
            $key = $key();
            $config->set('private_key', $key);
        }

        $append = [
            'iat' => time(),
            'iss' => get_base_url(),
        ];

        if($config->has('duration')) {
            $append['exp'] = strtotime($config['duration']);
        }

        $data =  array_merge(
            ['jti' => FirebaseJWT::urlsafeB64Encode(random_bytes(4))],
            (array) $payload,
            $append
        );

        $algorithm = $config->get('algorithm', 'HS256');
        return FirebaseJWT::encode($data, $key, $algorithm);
    }

    public function decode($token)
    {
        $config = $this->getConfig();
        $key = $config->get('public_key', $config->get('key'));
        if(is_callable($key))
        {
            $key = $key();
            $config->set('public_key', $key);
        }

        if(!isset($config['duration']))
        {
            FirebaseJWT::$leeway = INF;
        }

        $algorithm = $config->get('algorithm', 'HS256');
        return FirebaseJWT::decode($token, $key, (array) $algorithm);
    }

    public function verify($token)
    {
        try
        {
            $this->decode($token);
            return true;
        }
        catch(SignatureInvalidException $ex) { return false; }
        catch(InvalidArgumentException $ex) { return false; }
        catch(UnexpectedValueException $ex) { return false; }
        catch(BeforeValidException $ex) { return false; }
        catch(ExpiredException $ex) { return false; }
    }

    /**
     * @return Collection
     */
    public function getConfig()
    {
        return $this->config;
    }
}