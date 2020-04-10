<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class CheckoutStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public $validator = null;
    protected function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }
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
        return [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirmPassword' => 'required',
            'paymentMethodId' => 'required|exists:paymentMethod,id',
            'planId' => 'exists:plan,id',
            'terms' => 'required|accepted'
        ];
    }
    public function messages()
    {
        return [
            'terms.accepted' => 'Los términos y condiciones deben ser aceptados',
            'firstName.required' => 'Es requerido el nombre del usuario',
            'lastName.required' => 'Es requerido el apellido del usuario',
            'code.required' => 'Es requerido el RUT del usuario',
            'email.required' => 'Es requerido un correo electrónico',
            'email.email' => 'El formato es incorrecto',
            'email.unique' => 'Este email ya se encuentra registrado',
            'password.required' => 'Debe ingresar una contraseña para registrarse',
            'confirmPassword.required' => 'Debe confirmar la contraseña correctamente',
            'paymentMethodId.required' => 'Es requerido una forma de pago',
            'paymentMethodId.exists' => 'Es método de pago no es válido',
            'planId.required' => 'Es requerido seleccionar un plan',
            'planId.exists' => 'El plan seleccionado no es válido',
        ];
    }
}
