<?php

return [
    'accepted' => 'The :attribute field must be accepted.',
    'confirmed' => 'The :attribute field confirmation does not match.',
    'email' => 'The :attribute field must be a valid email address.',
    'exists' => 'The selected :attribute is invalid.',
    'in' => 'The selected :attribute is invalid.',
    'integer' => 'The :attribute field must be an integer.',
    'max' => [
        'array' => 'The :attribute field must not have more than :max items.',
        'file' => 'The :attribute field must not be greater than :max kilobytes.',
        'numeric' => 'The :attribute field must not be greater than :max.',
        'string' => 'The :attribute field must not be greater than :max characters.',
    ],
    'min' => [
        'array' => 'The :attribute field must have at least :min items.',
        'file' => 'The :attribute field must be at least :min kilobytes.',
        'numeric' => 'The :attribute field must be at least :min.',
        'string' => 'The :attribute field must be at least :min characters.',
    ],
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute field must be a string.',
    'unique' => 'The :attribute has already been taken.',
    'attributes' => [
        'body' => 'comment',
        'description' => 'story',
        'email' => 'email address',
        'locale' => 'language',
        'name' => 'name',
        'parent_id' => 'comment',
        'password' => 'password',
        'post_creator' => 'author',
        'reaction' => 'reaction',
        'title' => 'story title',
        'token' => 'reset token',
    ],
];
