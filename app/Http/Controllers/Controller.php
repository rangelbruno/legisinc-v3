<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * The middleware registered on the controller.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * Register middleware on the controller.
     */
    public function middleware($middleware, array $options = [])
    {
        foreach ((array) $middleware as $m) {
            $this->middleware[] = [
                'middleware' => $m,
                'options' => &$options,
            ];
        }

        return new class($options) {
            public function __construct(protected &$options) {}
            
            public function only($methods)
            {
                $this->options['only'] = is_array($methods) ? $methods : func_get_args();
                return $this;
            }

            public function except($methods)
            {
                $this->options['except'] = is_array($methods) ? $methods : func_get_args();
                return $this;
            }
        };
    }

    /**
     * Get the middleware assigned to the controller.
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }
}
