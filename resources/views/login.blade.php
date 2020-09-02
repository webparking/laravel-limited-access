<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@lang('LimitedAccess::login.document.title')</title>
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="/vendor/limited-access/login.css">
</head>
<body>
<main class="text-wrapper">
    <div class="title">
        {{ $errors->any() ? trans('LimitedAccess::login.incorrect_code') : trans('LimitedAccess::login.title') }}
    </div>

    <div class="subtitle">
        {{ $errors->any() ? trans('LimitedAccess::login.try_again') : trans('LimitedAccess::login.subtitle') }}
    </div>

    <form action="{{ route('LimitedAccess::login') }}" method="post">
        {{ csrf_field() }}
        <input name="code" type="text" class="{{ $errors->any() ? 'error' : null }}">
        <button>@lang('LimitedAccess::login.submit')</button>
    </form>

    <div class="contact">
        @lang('LimitedAccess::login.footer_text')
    </div>
</main>
</body>
</html>