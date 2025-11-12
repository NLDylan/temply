<?php

namespace App\Http\Requests\Resume;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBasicInformationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'headline' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'profile' => ['required', 'array'],
            'profile.full_name' => ['required', 'string', 'max:255'],
            'profile.email' => ['nullable', 'email', 'max:255'],
            'profile.phone' => ['nullable', 'string', 'max:255'],
            'profile.working_rights' => ['nullable', 'string', 'max:255'],
            'profile.contact_links' => ['nullable', 'array'],
            'profile.contact_links.*.id' => ['nullable', 'string'],
            'profile.contact_links.*.label' => ['nullable', 'string', 'max:100', 'required_with:profile.contact_links.*.url'],
            'profile.contact_links.*.url' => ['nullable', 'url', 'max:255'],
        ];
    }
}
