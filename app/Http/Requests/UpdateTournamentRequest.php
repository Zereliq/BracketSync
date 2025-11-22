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
            'seeding_type' => 'required|in:custom,avg_score,mp_percent,points,drawing',
            'win_condition' => 'required|in:scoreV2,scoreV1,acc,combo',
            'signup_method' => 'required|in:self,host',
            'staff_can_play' => 'boolean',
            'mode' => 'required|in:standard,fruit,piano,drums',
            'signup_restriction' => 'nullable|in:rank,avg-rank,badge-weighted',
            'rank_min' => 'nullable|integer',
            'rank_max' => 'nullable|integer|gte:rank_min',
            'country_restriction_type' => 'nullable|in:none,whitelist,blacklist',
            'country_list' => 'nullable|array',
            'country_list.*' => 'string|size:2',
            'signup_start' => 'nullable|date',
            'signup_end' => 'nullable|date|after:signup_start',
        ];
    }
}
