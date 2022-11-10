<x-mail::message>
# Dear {{$sharedUser->name}}

    {{$user->name}} shared some content with you, to view this content click on button below.

<x-mail::button :url="'http://127.0.0.1:8000/shared-content'">
    Preview content
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

{{--@component('mail::message')--}}

{{--    # Dear {{$sharedUser->name}}--}}

{{--    {{$user->name}} shared some content with you, to view this content click on button below.--}}

{{--    @component('mail::button', ['url' =>'http://127.0.0.1:8000/shared-content'])--}}
{{--        Preview content--}}
{{--    @endcomponent--}}

{{--    Thanks,<br>--}}
{{--    {{ config('app.name') }}--}}
{{--@endcomponent--}}
