<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BooksRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            return [
                'title' => 'required|string|max:155',
                'author' => 'required|string|max:255',
                'date' => 'required|date',
                // 'status' => 'required|in:available,borrowed',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // sometimes - cause if this field is available in request then only validate
            return [
                'title' => 'sometimes|required|string|max:155',
                'author' => 'sometimes|required|string|max:255',
                'date' => 'sometimes|required|date',
                'status' => 'sometimes|required|in:available,borrowed',
            ];
        }
        return [];
    }
}
