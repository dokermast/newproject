<?php

require_once ROOT.'/vendor/autoload.php';

class Router
{

	private $routes;

	public function __construct()
	{
		$routesPath = ROOT.'/config/routes.php';
		$this->routes = include($routesPath);
	}


	private function getURI()
	{
		if (!empty($_SERVER['REQUEST_URI'])) {
		return trim($_SERVER['REQUEST_URI'], '/');
		}
	}

	public function run()
	{
		$uri = $this->getURI();

        $uri = mb_stristr($uri, '/');
        $uri = mb_substr($uri, 1);

        if (stristr($uri,'?')){
            $uri = mb_stristr($uri, '?', true);
        }

        foreach ($this->routes as $uriPattern => $path) {

			if(preg_match("~$uriPattern~", $uri)) {

                /* Get internal path*/
				$internalRoute = preg_replace("~$uriPattern~", $path, $uri);

				$segments = explode('/', $internalRoute);

                $controllerName = array_shift($segments).'Controller';
				$controllerName = ucfirst($controllerName);

                $actionName = 'action'.ucfirst(array_shift($segments));
                $parameters = $segments;

				$controllerPath = ROOT . '/controllers/' .$controllerName. '.php';

                if (file_exists($controllerPath)) {
					include_once($controllerPath);
				}

                $methods = get_class_methods($controllerName);

                
                if (in_array($actionName, $methods)){

                    $controllerObject = new $controllerName;
                    $result = call_user_func_array(array($controllerObject, $actionName), $parameters);

                    if ($result != null) {
                        break;
                    }

                } else {

                    $loader = new \Twig\Loader\FilesystemLoader('views');
                    $twig = new \Twig\Environment($loader);

                    $template = $twig->load('error.html');
                    $error_message = "YOU try to call undefined page";
                    echo $template->render([ 'error_message' => $error_message]);

                    break;
                }
			}
        }
	}
}
