<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidarPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Para criação de um novo registro todos os campos devem ser validados
        if ($this->isMethod('POST')) {
            return [
                'titulo' => 'required|max:191',
                'descricao' => 'required',
            ];
        }

        // Quando for atualização de um registro, validar apenas o que foi enviado
        $validacao = [];

        if ($this->has('descricao')) {
            $validacao = ['descricao' => 'required'];
        }

        if ($this->has('titulo')) {
            $validacao = ['titulo' => 'required|max:191'];
        }

        return $validacao;
    }

    public function messages()
    {
        return [
            'titulo.required' => 'Campo obrigatório',
            'titulo.max' => 'O campo deverá conter no máximo :max',
            'descricao.required' => 'Campo obrigatório',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()
            ->json($validator->errors(), 400));
    }
}
