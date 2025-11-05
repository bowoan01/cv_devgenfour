<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;

class ContactSubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc,dns', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'min:20'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $key = 'contact:'.$this->ip();
        RateLimiter::hit($key, 60);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            abort(429, 'Too many submissions. Please try again later.');
        }
    }
}
