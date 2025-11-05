<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactSubmitRequest;
use App\Mail\ContactMessageSubmitted;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\LaravelSeo\Facades\Seo;

class ContactController extends Controller
{
    public function show()
    {
        Seo::title('Contact Devgenfour')
            ->description('Schedule a call with Devgenfour and share the vision for your next digital product.');

        $office = [
            'address' => Setting::getValue('contact_address', 'Remote-first, serving clients worldwide'),
            'email' => Setting::getValue('contact_email', config('mail.from.address')),
            'phone' => Setting::getValue('contact_phone', '+62-000-0000'),
        ];

        return view('site.contact', compact('office'));
    }

    public function submit(ContactSubmitRequest $request)
    {
        $message = ContactMessage::create($request->validated());

        $recipient = Setting::getValue('contact_email', config('mail.from.address'));
        if ($recipient) {
            Mail::to($recipient)->queue(new ContactMessageSubmitted($message));
        }

        RateLimiter::clear('contact:'.$request->ip());

        return back()->with('status', 'Thank you! Your message has been received.');
    }
}
