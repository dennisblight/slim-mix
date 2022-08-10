<?php
namespace Core\Middleware;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;

class TrimSlashes
{
    function __invoke(ServerRequest $request, $handler) {
		$uri = $request->getUri();
		$path = $uri->getPath();
		if($path != '/' && substr($path, -1) == '/')
		{
			// recursively remove slashes when its more than 1 slash
			while(substr($path, -1) == '/') {
				$path = substr($path, 0, -1);
			}

			// permanently redirect paths with a trailing slash
			// to their non-trailing counterpart
			$uri = $uri->withPath($path);
			$request = $request->withUri($uri);
		}

		return $handler->handle($request);
	}
}