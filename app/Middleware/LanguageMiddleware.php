<?php

namespace App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

class LanguageMiddleware {
	private $_languages = [];
	private $_defaultLanguage;

	/**
	 * Example middleware invokable class
	 *
	 * @param  Request  $request  PSR7 request
	 * @param  Response $response PSR7 response
	 * @param  callable $next     Next middleware
	 *
	 * @return Response
	 */
	public function __invoke($request, $response, $next) {
		$container           = $next->getContainer();
		$container['locale'] = new Language($container->settings['defaultLanguage']);
		$availableLanguages  = $container->settings['languages'];

		$virtualPath = $request->getUri()->getPath();
                //var_dump($virtualPath);
		$pathChunk   = explode("/", $virtualPath);
                
		if (count($pathChunk) > 1 && in_array($pathChunk[0], $availableLanguages)) {
                    
			$container['locale']->set($pathChunk[0]);
                        //var_dump(count($pathChunk));
                        //var_dump($pathChunk);
                        $num = count($pathChunk);
                        //var_dump(" num = " . $num);
			$pathChunk = array_slice($pathChunk, (1 - $num));
                        //var_dump($pathChunk);
		}

		$newPath = "/" . implode("/", $pathChunk);
                //var_dump($newPath);
		if ($request->getMethod() == 'GET') {
			$request = $request->withUri($request->getUri()->withPath($newPath));
                        //var_dump($request);
		}
                //var_dump($next($request, $response));
		return $next($request, $response);
	}

	public function __construct(array $languages, $defaultLanguage) {
		$this->_languages = $languages;
		$this->_defaultLanguage = $defaultLanguage;
	}
}