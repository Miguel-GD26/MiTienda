<?php

return [
    'accepted'             => 'El campo :attribute debe ser aceptado.',
    'active_url'           => 'El campo :attribute no es una URL válida.',
    'after'                => 'El campo :attribute debe ser una fecha posterior a :date.',
    'alpha'                => 'El campo :attribute solo puede contener letras.',
    'alpha_num'            => 'El campo :attribute solo puede contener letras y números.',
    'array'                => 'El campo :attribute debe ser un arreglo.',
    'before'               => 'El campo :attribute debe ser una fecha anterior a :date.',
    'between'              => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'file'    => 'El archivo :attribute debe pesar entre :min y :max kilobytes.',
        'string'  => 'El campo :attribute debe tener entre :min y :max caracteres.',
        'array'   => 'El campo :attribute debe tener entre :min y :max elementos.',
    ],
    'boolean'              => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed'            => 'La confirmación de :attribute no coincide.',
    'email'                => 'El campo :attribute debe ser un correo válido.',
    'exists'               => 'El campo :attribute no existe en nuestros registros.',
    'required'             => 'El campo :attribute es obligatorio.',
    'unique'               => 'El campo :attribute ya ha sido registrado.',
    'regex' => 'El campo :attribute debe contener exactamente 9 dígitos numéricos.',
    'min'                  => [
        'string'  => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'max'                  => [
        'string'  => 'El campo :attribute no debe exceder de :max caracteres.',
    ],
    'attributes'           => [
        'email'    => 'correo electrónico',
        'password' => 'contraseña',
        'name'     => 'nombre',
    ],
    'size' => [
    'string'  => 'El campo :attribute debe contener :size caracteres.',
    ],
    'min' => [
    'numeric' => 'El campo :attribute debe ser al menos :min.',
    'file' => 'El archivo :attribute debe tener al menos :min kilobytes.',
    'string' => 'Debe contener al menos :min caracteres.',
    'array' => 'El campo :attribute debe tener al menos :min elementos.',
    ],
    'digits' => 'Debe tener :digits dígitos.',
    'required_if' => 'El campo :attribute es obligatorio',
    'custom' => [
        'empresaOption' => [
            'required' => 'Debes seleccionar una empresa.',
        ],
    ],
    'image' => 'El archivo debe ser una imagen válida.',
    'max'   => [
        'file' => 'El archivo no puede superar los :max KB.',
    ],
];