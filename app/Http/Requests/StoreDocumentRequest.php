<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validate uploaded documents.
     *
     * Allowed types: PDF, DOCX, DOC, TXT, and common image formats.
     * Max size: 20 MB.
     */
    public function rules(): array
    {
        return [
            'document' => [
                'required',
                'file',
                'max:20480',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    $allowed = ['pdf', 'docx', 'doc', 'txt', 'png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'];
                    if (!in_array($ext, $allowed)) {
                        $fail('Allowed types: PDF, DOCX, DOC, TXT, PNG, JPG, GIF, BMP, WEBP.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'document.max' => 'The document must not exceed 20 MB.',
        ];
    }
}
