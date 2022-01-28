<?php

declare(strict_types=1);

namespace Webparking\LimitedAccess\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webparking\LimitedAccess\Codes;

class LimitedAccessRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Codes $codes */
        $codes = app(Codes::class);

        return [
            'code' => [
                'required',
                Rule::in($codes->get()),
            ],
        ];
    }
}
