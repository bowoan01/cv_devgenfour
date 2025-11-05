@component('mail::message')
# New Contact Message

**Name:** {{ $contactMessage->name }}  
**Email:** {{ $contactMessage->email }}  
@if($contactMessage->company)
**Company:** {{ $contactMessage->company }}  
@endif
@if($contactMessage->phone)
**Phone:** {{ $contactMessage->phone }}  
@endif

@component('mail::panel')
{{ $contactMessage->message }}
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
