<?php

namespace RecurLoop\Recaptcha\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use RecurLoop\Recaptcha\Exceptions\InvalidTokenException;
use RecurLoop\Recaptcha\Facades\Recaptcha;

class VerifyRecaptchaScore
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next, ?string $action = null, ?float $minScore = null)
    {
        if (
            $this->runningUnitTests() ||
            Recaptcha::checkScore($request, $action, $minScore)
        ) {
            return $next($request);
        }

        throw new InvalidTokenException('Recaptcha score not reached.');
    }

    /**
     * Determine if the application is running unit tests.
     *
     * @return bool
     */
    protected function runningUnitTests()
    {
        return $this->app->runningInConsole() || $this->app->runningUnitTests();
    }
}
