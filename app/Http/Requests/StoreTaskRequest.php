<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in the service layer
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'start_time' => ['nullable', 'string', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'due_time' => ['nullable', 'string', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'priority' => ['nullable', 'string', 'in:low,medium,high'],
            'notify' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'title.max' => 'Task title must not exceed 255 characters.',
            'start_time.regex' => 'Start time must be in HH:MM format (24-hour).',
            'due_date.after_or_equal' => 'Due date must be on or after the start date.',
            'due_time.regex' => 'Due time must be in HH:MM format (24-hour).',
            'color.regex' => 'Color must be a valid hex color code (e.g., #FF0000).',
            'priority.in' => 'Priority must be one of: low, medium, high.',
        ];
    }
}

