@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])

<div style="display:block;align-items:center;position:relative;margin-top:20px;height:30px">
    <img src="{{asset('logo-icon.png')}}" alt="" style="height:100%;float: left;">
    <img src="{{asset('app-logo.png')}}" alt="" style="width:80px;float: left;transform:translateY(-50%);top:50%;margin-left:10px">
</div>

@endcomponent
@endslot


<h1 style="color:#007D67">Forgot Password</h1>

Hi {{$firstname}}, You are receiving this email because we received a password reset request for your account associated with {{$email}}.


<div style="display: inline-block; margin-top:20px; margin-bottom:30px;">
    <?php foreach (str_split($otp) as $digit) { ?>
        <div style="float: left;border: 1px solid #007D67; padding:10px 15px; border-radius:5px; margin-right:10px">
            <span>{{$digit}}</span>
        </div>
    <?php } ?>
</div>


If you did not request a password reset, no further action is required

Regards, <br/>
Team {{ config('app.name') }}

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
