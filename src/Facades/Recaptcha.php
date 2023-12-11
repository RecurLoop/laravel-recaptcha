<?php

namespace RecurLoop\Recaptcha\Facades;

use Illuminate\Support\Facades\Facade;
use RecurLoop\Recaptcha\Recaptcha as RecaptchaInstance;

/**
 * Backend Helper
 * @method static ?float retrieveScore(\Illuminate\Http\Request $request, ?string $action = null)
 * @method static void checkScore(\Illuminate\Http\Request $request, ?string $action = null, ?float $minScore = null)
 *
 * Front Javascript Helper
 * @method static string initJs()
 * @method static string addTokenJs(string $action, string $formJs = 'this', string $thenJs = '')
 * @method static string onClickSubmitJs(string $action)
 *
 * @see \App\Services\Recaptcha
 */
class Recaptcha extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return RecaptchaInstance::class;
    }
}
