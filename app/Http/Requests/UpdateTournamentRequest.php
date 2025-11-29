<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTournamentRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'edition' => 'nullable|string|max:100',
            'abbreviation' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:5000',
            'qualifier_results_public' => 'boolean',
            'elim_type' => 'required|in:single,double,caterpillar',
            'bracket_size' => 'required|integer|in:8,16,32,64,128',
            'auto_bracket_size' => 'boolean',
            'format' => 'required|integer|min:1|max:8',
            'min_teamsize' => 'required|integer|min:1|max:8',
            'max_teamsize' => 'required|integer|min:1|max:8|gte:min_teamsize',
            'has_qualifiers' => 'boolean',
            'seeding_type' => 'required|in:rank,custom,avg_score,mp_percent,points,drawing',
            'win_condition' => 'required|in:scoreV2,scoreV1,acc,combo',
            'signup_method' => 'required|in:self,invitationals',
            'staff_can_play' => 'boolean',
            'mode' => 'required|in:standard,fruit,piano,drums',
            'signup_restriction' => 'nullable|in:rank,avg-rank,badge-weighted',
            'rank_min' => [
                'nullable',
                'integer',
                'min:1',
                'required_if:signup_restriction,rank,avg-rank,badge-weighted',
            ],
            'rank_max' => [
                'nullable',
                'integer',
                'min:1',
                'gte:rank_min',
                'required_if:signup_restriction,rank,avg-rank,badge-weighted',
            ],
            'country_restriction_type' => 'nullable|in:none,whitelist,blacklist',
            'country_list_input' => [
                'nullable',
                'string',
                'required_if:country_restriction_type,whitelist,blacklist',
            ],
            'signup_start' => 'nullable|date',
            'signup_end' => 'nullable|date|after:signup_start',
        ];
    }

    /**
     * Get custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The tournament name is required.',
            'name.max' => 'The tournament name cannot exceed 255 characters.',
            'edition.max' => 'The edition cannot exceed 100 characters.',
            'abbreviation.max' => 'The abbreviation cannot exceed 20 characters.',
            'description.max' => 'The description cannot exceed 5000 characters.',
            'mode.required' => 'Please select a game mode.',
            'mode.in' => 'The selected game mode is invalid.',
            'elim_type.required' => 'Please select an elimination type.',
            'bracket_size.required' => 'Please select a bracket size.',
            'format.required' => 'The format field is required.',
            'format.min' => 'The format must be at least 1.',
            'format.max' => 'The format cannot exceed 8.',
            'min_teamsize.required' => 'The minimum team size is required.',
            'min_teamsize.min' => 'The minimum team size must be at least 1.',
            'min_teamsize.max' => 'The minimum team size cannot exceed 8.',
            'max_teamsize.required' => 'The maximum team size is required.',
            'max_teamsize.min' => 'The maximum team size must be at least 1.',
            'max_teamsize.max' => 'The maximum team size cannot exceed 8.',
            'max_teamsize.gte' => 'The maximum team size must be greater than or equal to the minimum team size.',
            'seeding_type.required' => 'Please select a seeding type.',
            'win_condition.required' => 'Please select a win condition.',
            'signup_method.required' => 'Please select a signup method.',
            'rank_min.required_if' => 'The minimum rank is required when using rank restrictions.',
            'rank_min.integer' => 'The minimum rank must be a valid number.',
            'rank_min.min' => 'The minimum rank must be at least 1.',
            'rank_max.required_if' => 'The maximum rank is required when using rank restrictions.',
            'rank_max.integer' => 'The maximum rank must be a valid number.',
            'rank_max.min' => 'The maximum rank must be at least 1.',
            'rank_max.gte' => 'The maximum rank must be greater than or equal to the minimum rank.',
            'country_list_input.required_if' => 'Please enter country codes when using country restrictions.',
            'signup_end.after' => 'The signup end date must be after the signup start date.',
        ];
    }
}
