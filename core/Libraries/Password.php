<?php
namespace Core\Libraries;

use DI\Container;
use Core\Collection;

class Password
{
    /** @var Collection */
    private $config;

    private $key = null;
    
    public function __construct(Container $container)
    {
        $this->config = $container->get('settings')->get('password', new Collection());
    }

    public function hash($password)
    {
        $algo = $this->config->get('algo', PASSWORD_BCRYPT);
        $password = $this->transform($password);

        switch($algo)
        {
            case PASSWORD_DEFAULT:
            case PASSWORD_BCRYPT:
                $cost = $this->config->get('cost', 10);
                return password_hash($password, $algo, ['cost' => $cost]);
            case 'md5':
                return md5($password);
            case 'sha1':
                return sha1($password);
        }

        if(in_array($algo, hash_algos()))
        {
            return hash($algo, $password);
        }
        
        return $password;
    }

    public function checkHash($password, $hash)
    {
        $algo = $this->config->get('algo', PASSWORD_BCRYPT);

        $password = $this->transform($password);

        switch($algo)
        {
            case PASSWORD_DEFAULT:
            case PASSWORD_BCRYPT:
                return password_verify($password, $hash);
            case 'md5':
                return string_equals(md5($password), $hash);
            case 'sha1':
                return string_equals(sha1($password), $hash);
        }

        if(in_array($algo, hash_algos()))
        {
            return string_equals(hash($algo, $password),  $hash);
        }
        
        return string_equals($password, $hash);
    }

    private function transform($password)
    {
        $salt = $this->config->get('salt');
        $mask = $this->config->get('mask');

        if(!empty($mask))
        {
            $password = $this->mask($password, $mask);
        }

        if(!empty($salt))
        {
            $password = $salt . '.' . $password;
        }

        return $password;
    }

    private function mask($string, $mask)
    {
        $newString = '';
        $offset = 0;
        $found = 0;
        for($i = 0; $i < strlen($string); $i++)
        {
            $found = 0;
            while(ord($string[$i]) == ord($mask[$offset]))
            {
                $offset = ($offset + 1) % strlen($mask);
                if($found++ >= strlen($mask))
                {
                    return $string;
                }
            }
            $newString .= chr((ord($string[$i]) ^ ord($mask[$offset])) & 0xff);
            $offset++;
        }
        return $newString;
    }
    

    public function getKey()
    {
        if(is_null($this->key))
        {
            $this->key = $this->config->get('key', $this->config->get('appKey', ''));
        }

        return $this->key;
    }
}