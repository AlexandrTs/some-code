<?php

namespace App\Providers;

use App\Rules\BailingValidationRule;
use App\Rules\OrderAmountRule;
use App\Rules\OrderExistsRule;
use App\Services\API\EcomOrderAPIService;
use App\Services\API\JysanLoansAPIService;
use App\Services\CsCartLogEventService;
use App\Services\RequestResponseAPILoggerService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Translation\Translator;
use App\Validation\BailingValidator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CsCartLogEventService::class);
        $this->app->alias(CsCartLogEventService::class, 'csevent');

        $this->app->bind(
            'App\Contracts\APILoggerInterface',
            'App\Services\RequestResponseAPILoggerService'
        );

        $this->app->bind('EcomOrderAPI', function(){
            return new EcomOrderAPIService();
        });

        $this->app->bind('JysanLoansAPI', function(){
            return new JysanLoansAPIService();
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * @var \Illuminate\Validation\Factory $factory
         */
        $factory = resolve(Factory::class);
        $factory->resolver(function (
                Translator $translator,
                array $data,
                array $rules,
                array $messages,
                array $customAttributes
            ) {
                return new BailingValidator($translator, $data, $rules, $messages, $customAttributes);
            }
        );

    }
}
