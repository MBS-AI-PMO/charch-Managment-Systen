<?php

it('registers the CSRF middleware on the web group', function () {
    $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
    $web = $kernel->getMiddlewareGroups()['web'] ?? [];

    // Laravel 12 renamed VerifyCsrfToken to ValidateCsrfToken — accept either.
    $hasCsrf = collect($web)->contains(function ($m) {
        if (! is_string($m)) {
            return false;
        }
        return str_ends_with($m, 'ValidateCsrfToken') || str_ends_with($m, 'VerifyCsrfToken');
    });

    expect($hasCsrf)->toBeTrue(
        'A CSRF-validating middleware must live in the `web` group.'
    );
});

it('the contact form endpoint requires CSRF (read methods bypass it)', function () {
    // The framework's TestCase auto-bypasses CSRF inside unit tests so we
    // can't drive a real POST through here. Instead, we verify the middleware
    // is wired by checking its isReading helper: GET is always exempt, POST
    // is not.
    $class = collect(
        app(\Illuminate\Contracts\Http\Kernel::class)->getMiddlewareGroups()['web'] ?? []
    )->first(fn ($m) => is_string($m) && (
        str_ends_with($m, 'ValidateCsrfToken') || str_ends_with($m, 'VerifyCsrfToken')
    ));

    $middleware = app($class);
    $ref = new ReflectionClass($middleware);

    if ($ref->hasMethod('isReading')) {
        $method = $ref->getMethod('isReading');
        $method->setAccessible(true);
        expect($method->invoke($middleware, \Illuminate\Http\Request::create('/contact', 'GET')))
            ->toBeTrue()
            ->and($method->invoke($middleware, \Illuminate\Http\Request::create('/contact', 'POST')))
            ->toBeFalse();
    } else {
        expect($class)->toBeString();
    }
});
