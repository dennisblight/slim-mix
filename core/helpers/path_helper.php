<?php

if(!function_exists('normalize_path'))
{
    function normalize_path($path, $trimSeparator = true)
    {
        $path = str_replace(
            ['/', '\\'],
            [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR],
            $path
        );

        while (strpos($path, DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) !== false)
        {
            $path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
        }

        return $trimSeparator ? rtrim($path, DIRECTORY_SEPARATOR) : $path;
    }
}

if(!function_exists('join_path'))
{
    function join_path()
    {
        $paths = array_map(function($path) {
            if($path == '/' || $path == '\\') return $path;
            while(str_starts_with($path, ' ') || str_starts_with($path, '/') || str_starts_with($path, '\\'))
            {
                $path = ltrim($path, ' /\\');
            }
            while(str_ends_with($path, ' ') || str_ends_with($path, '/') || str_ends_with($path, '\\'))
            {
                $path = rtrim($path, ' /\\');
            }
            return $path;
        }, func_get_args());

        return normalize_path(join(DIRECTORY_SEPARATOR, $paths));
    }
}

if(!function_exists('rglob'))
{
    function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags); 
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}