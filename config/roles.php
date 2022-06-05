<?php

return [

    'roles' => [
        'superadmin' => [
            'users-register',
            'users-modify-any',
            'users-change-roles-any',
            'credentials-create',
            'credentials-modify-any',
        ],
        'user' => [
            'credentials-create',
            'credentials-modify',
        ]
    ]

];
