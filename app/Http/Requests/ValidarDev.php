<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; // para implementar a function: failedValidation() que ao invés de redirecionar retornará uma resposta json
use Illuminate\Http\Exceptions\HttpResponseException; // para retornar a resposta em json que sera implementada na function failedValidation()

class ValidarDev extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /**
         * Se o método authorize retornar false, uma resposta HTTP com um código de status 403 será retornada automaticamente e o método do controlador não será executado.
         * Se você planeja ter lógica de autorização em outra parte do seu aplicativo, retorne true no método authorize:
         * return true;
         */
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('id');

        return [
            'github_username' => 'required|unique:devs,github_username,' . $id . '|max:191',
            'nome' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'github_username.unique'
            => 'username já cadastrado por outro usuário',
            'github_username.required' => 'Campo obrigatório',
            'github_username.max' => 'O campo deverá conter no máximo :max',
            'nome.required' => 'Campo obrigatório',
        ];
    }

    // Não esqueça de adicionar as uses:
    /*
    * use Illuminate\Contracts\Validation\Validator; // para implementar a function: failedValidation() que ao invés de redirecionar retornará uma resposta json
    * use Illuminate\Http\Exceptions\HttpResponseException; // para retornar a resposta em json que sera implementada na function faile
    */

    protected function failedValidation(Validator $validator)
    {
        // retorna o Json com os erros de validação e resposta de status 400 (Bad request)
        throw new HttpResponseException(response()
            ->json($validator->errors(), 400));
    }
}
