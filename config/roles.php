<?php

return [

    'roles' => [
        'superadmin' => [
            'users-register',
            'users-modify-any',
            'users-change-roles-any',
            'groups-create',
            'groups-modify-any',
            'credentials-create',
            'credentials-modify-any',
            'notes-create',
            'notes-modify-any'
        ],
        'user' => [
            'groups-create',
            'groups-modify',
            'credentials-create',
            'credentials-modify',
            'notes-create',
            'notes-modify'
        ]
    ]

];
