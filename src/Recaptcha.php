<?php

namespace RecurLoop\Recaptcha;

use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RecurLoop\Recaptcha\Exceptions\InvalidTokenException;

class Recaptcha
{
    protected Client $httpClient;

    protected bool $enabled;

    protected string $origin;

    protected string $siteKey;

    protected string $secretKey;

    protected string $inputName;

    protected string $locale;

    protected ?float $score = null;

    /**
     * Recaptcha constructor.
     *
     * @return void
     */
    public function __construct(Client $httpClient, Application $app)
    {
        $this->httpClient = $httpClient;
        $this->enabled    = config('recaptcha.enabled');
        $this->origin     = config('recaptcha.origin');
        $this->siteKey    = config('recaptcha.site_key');
        $this->secretKey  = config('recaptcha.secret_key');
        $this->inputName  = config('recaptcha.input_name');
        $this->locale     = config('recaptcha.locale') ?? $app->getLocale();
    }

    /**
     * Verify the given token and return the score.
     *
     * @param \Illuminate\Http\Request $request
     * @param ?string $action
     * @return float
     *
     * @throws InvalidTokenException
     */
    public function retrieveScore(Request $request, ?string $action = null): float
    {
        if (!$this->enabled) {
            return 1.0;
        }

        if ($this->score !== null) {
            return $this->score;
        }

        $token = $request->input($this->inputName);

        $remoteIp = $request->getClientIp();

        if (!is_string($token) || !is_string($remoteIp)) {
            throw new InvalidTokenException("Recaptcha token is invalid.");
        }

        $response = $this->httpClient->request('POST', $this->origin . '/api/siteverify', [
            'form_params' => [
                'secret'   => $this->secretKey,
                'response' => $token,
                'remoteip' => $remoteIp,
            ],
        ]);

        $recaptchaData = json_decode($response->getBody());

        $recaptchaData->success ??= false;
        $recaptchaData->action  ??= null;
        $recaptchaData->score   ??= null;

        if (!$recaptchaData->success || ($action !== null && ($this->encodeAction($action) !== $recaptchaData->action)) || !$recaptchaData->score) {
            throw new InvalidTokenException("Recaptcha token is invalid.");
        }

        return $this->score = $recaptchaData->score;
    }

    /**
     * Verify the given token and check the score.
     *
     * @param \Illuminate\Http\Request $request
     * @param ?float $minScore
     * @param ?string $action
     * @return void
     *
     * @throws InvalidTokenException
     */
    public function checkScore(Request $request, ?string $action = null, ?float $minScore = null): void
    {
        $score = $this->retrieveScore($request, $action);

        $minScore ??= config('recaptcha.default.min_score');

        if ($score >= $minScore) {
            throw new InvalidTokenException('Recaptcha score not reached.');
        }
    }

    /**
     * Html helper.
     *
     * @return string
     */
    public function initJs(): string
    {
        if (!$this->enabled) {
            return '';
        }

        return <<<HTML
            <script src="{$this->origin}/api.js?hl='{$this->locale}&render={$this->siteKey}"></script>
        HTML;
    }

    /**
     * javascript helper.
     *
     * @param string $action
     * @param string $thenJs
     *
     * @return string
     */
    public function addTokenJs(string $action, string $formJs = 'this', string $thenJs = ''): string
    {
        if (!$this->enabled) {
            return '';
        }

        $action = $this->encodeAction($action);

        return <<<JAVASCRIPT
            grecaptcha.ready(function() {
                grecaptcha.execute('{$this->siteKey}', {action: '{$action}'}).then(function(token) {
                    recaptchaInput = document.createElement('input');
                    recaptchaInput.type = 'hidden';
                    recaptchaInput.name = '{$this->inputName}';
                    recaptchaInput.value = token;
                    {$formJs}.appendChild(recaptchaInput);

                    {$thenJs}
                }.bind(this));
            }.bind(this));
        JAVASCRIPT;
    }

    /**
     * javascript helper (ATTENTION: 'return false;' to avoid event propagation).
     *
     * @param string $action
     * @param string $key
     *
     * @return string
     */
    public function onClickSubmitJs(string $action): string
    {
        $then = <<<JAVASCRIPT
            this.form.submit();
        JAVASCRIPT;

        return $this->addTokenJs($action, 'this.form', $then) . <<<JAVASCRIPT
            return false;
        JAVASCRIPT;
    }

    /**
     * Encode action.
     *
     * Fix action string for recaptcha
     *
     * @param string $action
     *
     * @return string
     */
    protected function encodeAction(string $action): string
    {
        return preg_replace( '/[^a-z0-9]/i', '_', Str::lower($action));
    }
}
