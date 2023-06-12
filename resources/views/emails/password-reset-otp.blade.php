<x-mail::message>
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<!-- header here -->
<img src="{{ asset($company->logo) }}" class="logo" alt="Logo">
@endcomponent
@endslot
# Password Rest Otp

Your password reset OTP is {{ $otp }}

Thanks,<br>
{{ $company->name }}
@slot('footer')
@component('mail::footer')
© {{ date("Y") }} {{ $company->name }}. All rights reserved.
@endcomponent
@endslot
</x-mail::message>