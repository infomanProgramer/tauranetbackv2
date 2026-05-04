<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\User;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function expectsJson()
    {
        return true;
    }
    public function authorize()
    {
        return true;
    }

    public function wantsJson()
    {
        return true;
    }
    public function rules()
    {
        return [
            'primer_nombre' => 'required_without_all:segundo_nombre',
            'segundo_nombre' => 'required_without_all:primer_nombre',
            'paterno' => 'required_without_all:materno',
            'materno' => 'required_without_all:paterno',
            //'email' => 'email|unique:users|nullable',
            //'dni' => 'unique:users|min:3|max:100|nullable',
            //'sexo' => 'required|boolean',
            //'direccion' => 'required',
            'nombre_usuario' => 'required|unique:users|min:4|max:12',
            'password' => 'required|min:6|confirmed',
            //'fecha_nac' => 'date:dd/mm/YYYY|nullable',
            //'celular' => 'required_without_all:telefono',
            //'telefono' => 'required_without_all:celular',
            'tipo_usuario' => 'required'
        ];
    }

    public function rulesUpdate($id_user)
    {
        $user = User::find($id_user);
        $rules = [
            'primer_nombre' => 'required_without_all:segundo_nombre',
            'segundo_nombre' => 'required_without_all:primer_nombre',
            'paterno' => 'required_without_all:materno',
            'materno' => 'required_without_all:paterno',
            //'email' => 'email|unique:users,email,'.$user->id_usuario.',id_usuario|nullable',
            //'dni' => 'unique:users,dni,'.$user->id_usuario.',id_usuario|min:3|max:100|nullable',
            //'sexo' => 'required|boolean',
            //'estado' => 'required|boolean',
            //'direccion' => 'required',
            'nombre_usuario' => 'required|unique:users,nombre_usuario,'.$user->id_usuario.',id_usuario|min:4|max:12',
            //'fecha_nac' => 'date:dd/mm/YYYY|nullable',
            //'celular' => 'required_without_all:telefono',
            //'telefono' => 'required_without_all:celular',
            'tipo_usuario' => 'required'
        ];

        return $rules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'primer_nombre.required_without_all' => 'El campo Primer Nombre o Segundo Nombre es requerido',
            'primer_nombre.min' => 'El Primer Nombre tiene que tener 4 caracteres como mínimo',
            'primer_nombre.max' => 'El Primer Nombre tiene que tener 100 caracteres como maximo',

            'segundo_nombre.required_without_all' => 'El campo Primer Nombre o Segundo Nombre es requerido',
            'segundo_nombre.min' => 'El Segundo Nombre tiene que tener 4 caracteres como mínimo',
            'segundo_nombre.max' => 'El Segundo Nombre tiene que tener 100 caracteres como maximo',

            'paterno.required_without_all' => 'El campo Ap. Paterno o Materno es requerido',
            'paterno.min' => 'El Ap. Paterno tiene que tener 4 caracteres como mínimo',
            'paterno.max' => 'El Ap. Paterno tiene que tener 100 caracteres como maximo',

            'materno.required_without_all' => 'El campo Ap. Materno o Materno es requerido',
            'materno.min' => 'El Ap. Materno tiene que tener 4 caracteres como mínimo',
            'materno.max' => 'El Ap. Materno tiene que tener 100 caracteres como maximo',

            'email.required' => 'El campo Correo es requerido',
            'email.email' => 'El Correo no tiene el formato correcto',
            'email.unique' => 'El Correo ya existe',

            'dni.required' => 'El DNI es requerido',
            'dni.unique' => 'El DNI ya existe',
            'dni.min' => 'El DNI tiene que tener 4 caracteres como mínimo',
            'dni.max' => 'El DNI tiene que tener 100 carateres como maximo',

            'sexo.required' => 'El campo Sexo es requerido',
            'sexo.boolean' => 'El campo Sexo debe ser booleano',

            'estado.required' => 'El campo Estado es requerido',
            'estado.boolean' => 'El campo Estado debe ser booleano',
            
            'direccion.required' => 'La Dirección es requerida',

            'nombre_usuario.required' => 'El nombre de usuario es requerido',
            'nombre_usuario.unique' => 'El nombre de usuario ya existe',
            'nombre_usuario.min' => 'El nombre de usuario tiene que tener 4 caracteres como mínimo',
            'nombre_usuario.max' => 'El nombre de usuario tiene que tener 12 caracteres como maximo',

            'correo.required' => 'El Correo es requerido',
            'correo.email' => 'El Correo no tiene el formato correcto',

            'password.required' => 'El Passoword es requerido',
            'password.min' => 'El Password tiene que tener 6 caracteres como mínimo',
            'password.confirmed' => 'El Password debé ser confirmado',

            'fecha_nac.required' => 'La fecha de nacimiento es requerida',
            'fecha_nac.date' => 'La fecha no tiene el formato correcto',

            'celular.required_without_all' => 'El campo Celular o Telefono es requerido',
            'celular.min' => 'El Celular tiene que tener 4 caracteres como mínimo',
            'celular.max' => 'El Celular tiene que tener 20 caracteres como maximo',
            'celular.digits_between' => 'El Celular solo tiene que tener digitos del 0 - 9',

            'telefono.required_without_all' => 'El campo Celular o Telefono es requerido',
            'telefono.min' => 'El Telefono tiene que tener 4 caracteres como mínimo',
            'telefono.max' => 'El Telefono tiene que tener 20 caracteres como maximo',
            'telefono.digits_between' => 'El Telefono solo tiene que tener digitos del 0 - 9',
            'tipo_usuario.required' => 'El tipo de usuario es requerido'
        ];
    }
}
