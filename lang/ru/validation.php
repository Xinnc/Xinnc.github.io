<?php

return [

    'required' => 'Поле :attribute обязательно для заполнения.',
    'numeric' => 'Поле :attribute должно быть числом',
    'string' => 'Поле :attribute должно быть строкой.',
    'integer' => 'Поле :attribute должно быть целым числом.',
    'boolean' => 'Поле :attribute должно быть логическим значением (1/0).',
    'date' => 'Поле :attribute должно быть корректной датой.',
    'date_format' => 'Поле :attribute должно соответствовать формату: :format.',
    'after_or_equal' => 'Поле :attribute должно быть датой после или равной :date.',
    'before_or_equal' => 'Поле :attribute должно быть датой до или равной :date.',
    'after' => 'Поле :attribute должно быть датой после :date.',
    'before' => 'Поле :attribute должно быть датой до :date.',
    'exists' => 'Выбранный :attribute не существует или недоступен.',
    'unique' => 'Поле :attribute с таким названием уже существует.',

    'max' => [
        'string' => 'Поле :attribute не должно превышать :max символов.',
    ],

    'min' => [
        'string' => 'Поле :attribute должно содержать минимум :min символов.',
        'numeric' => 'Поле :attribute должно быть не меньше :min.',
    ],

    'attributes' => [
        'name' => 'название',
        'is_active' => 'активность',
    ],

];
