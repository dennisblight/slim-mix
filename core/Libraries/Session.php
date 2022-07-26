<?php
namespace Core\Libraries;

class Session extends \SlimSession\Helper
{
    
    public function flash($key, $value)
    {
        if(!isset($_SESSION['_']))
        {
            $_SESSION['_'] = [];
        }

        $_SESSION['_'][$key] = true;
        return $this->set($key, $value);
    }

    public function get($key, $default = null)
    {
        $result = parent::get($key, $default);
        if(isset($_SESSION['_'], $_SESSION['_'][$key]))
        {
            unset($_SESSION['_'][$key]);
            $this->delete($key);
        }
        return $result;
    }

    public function count()
    {
        $result = count($_SESSION);
        if(isset($_SESSION['_']))
        {
            $result--;
        }
        return $result;
    }
}