<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJamQueueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'spotify_uri' => ['required', 'string', 'regex:/^spotify:track:[a-zA-Z0-9]+$/'],
            'track_name' => ['required', 'string', 'max:255'],
            'artist_name' => ['required', 'string', 'max:255'],
            'album_name' => ['nullable', 'string', 'max:255'],
            'album_image_url' => ['nullable', 'string', 'max:500'],
            'duration_ms' => ['nullable', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Convert empty strings to null for nullable fields
        $this->merge([
            'album_name' => $this->album_name ?: null,
            'album_image_url' => $this->album_image_url ?: null,
            'duration_ms' => $this->duration_ms ?: null,
        ]);
    }
}
