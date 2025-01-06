<?php

use Faker\Factory as Faker;

$sharedData = [];

it('logs in successfully and stores the token', function () use (&$sharedData) {
    $response = $this->postJson('/api/login', [
        'email' => 'admin@gmail.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'access_token',
            ],
        ]);

    $sharedData['token'] = $response->json('data.access_token');
});

it('fetches the subscriptions list and stores a subscription UUID', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['token'], 'Token is missing!');

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['token']}",
    ])->getJson('/api/subscriptions');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['uuid'],
            ],
        ]);

    $sharedData['subscription_uuid'] = $response->json('data.0.uuid');
});

it('fetches the organizations list', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['token'], 'Token is missing!');

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['token']}",
    ])->getJson('/api/organization/list');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'organizations' => [
                    '*' => [
                        'uuid',
                        'title',
                        'description',
                        'registration_number',
                        'city',
                        'state',
                        'avatar',
                        'subscription_details' => [
                            '*' => [
                                'uuid',
                                'status',
                                'subscription' => [
                                    'uuid',
                                    'name',
                                ],
                            ],
                        ],
                        'user_info' => [
                            'uuid',
                            'username',
                            'email',
                            'phone',
                            'tenant_id',
                            'role',
                        ],
                    ],
                ],
            ],
        ]);

    $organization = $response->json('data.organizations');
    $this->assertNotEmpty($organization, 'Organization list is empty!');
    $randomIndex = array_rand($organization);
    $randomOrganization = $organization[$randomIndex];
    $randomOrganizationUuid = $randomOrganization['uuid'];

    $statuses = 'cancel';

    $sharedData['organization_uuid'] = $randomOrganizationUuid;
    $sharedData['organization_status'] = $statuses;
});

it('creates an organization using the subscription UUID and stores the admin email', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['token'], 'Token is missing!');
    $this->assertNotEmpty($sharedData['subscription_uuid'], 'Subscription UUID is missing!');

    $faker = Faker::create();
    $adminInfo = [
        'fname' => $faker->firstName,
        'lname' => $faker->lastName,
        'username' => $faker->userName,
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->phoneNumber,
    ];

    $data = [
        'title' => $faker->company,
        'registration_number' => $faker->bothify('##??##'),
        'description' => $faker->sentence,
        'address' => $faker->address,
        'city' => $faker->city,
        'state' => $faker->state,
        'phone' => $faker->phoneNumber,
        'zip' => $faker->postcode,
        'status' => '1',
        'admin_info' => $adminInfo,
        'subscription' => [
            'subscription_id' => $sharedData['subscription_uuid'],
            'billing_start_date' => $faker->date('Y-m-d', '+1 year'),
            'setup_fee' => $faker->numberBetween(500, 5000),
            'setup_fee_start_date' => $faker->date('Y-m-d', '+1 year'),
        ],
    ];

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['token']}",
    ])->postJson('/api/organization/store', $data);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'tenant_id',
                'title',
                'registration_number',
                'description',
                'address',
                'city',
                'state',
                'phone',
                'zip',
                'status',
                'uuid',
                'updated_at',
                'created_at',
                'id',
            ],
        ]);

    $sharedData['admin_email'] = $adminInfo['email'];
});

it('resets the password for the created admin user', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['admin_email'], 'Admin email is missing!');

    $forgotPasswordResponse = $this->postJson('/api/forgot-password', [
        'email' => $sharedData['admin_email'],
        'url' => 'https://example.com/reset-password',
    ]);

    $forgotPasswordResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Email has been sent Successfully.',
        ]);

    $token = DB::table('password_reset_tokens')
        ->where('email', $sharedData['admin_email'])
        ->first()
        ->token;

    $newPassword = 'newpassword';

    $resetPasswordResponse = $this->postJson('/api/reset-password', [
        'email' => $sharedData['admin_email'],
        'token' => $token,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);

    $resetPasswordResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);

    $sharedData['admin_password'] = $newPassword;
});

it('updates the organization', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['token'], 'Token is missing!');
    $this->assertNotEmpty($sharedData['organization_uuid'], 'organization uuid is missing!');
    $this->assertNotEmpty($sharedData['organization_status'], 'organization status is missing!');

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['token']}",
        'Content-Type' => 'multipart/form-data',
    ])->post('/api/organization/update-status', [
        'organization_id' => $sharedData['organization_uuid'],
        'status' => $sharedData['organization_status'],
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [],
        ]);
});

it('fetches users list for the organization admin', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['admin_email'], 'Admin email is missing!');
    $this->assertNotEmpty($sharedData['admin_password'], 'Admin password is missing!');

    $loginResponse = $this->postJson('/api/login', [
        'email' => $sharedData['admin_email'],
        'password' => $sharedData['admin_password'],
    ]);

    $loginResponse->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'access_token',
                'tenant_user_id',
            ],
        ]);

    $token = $loginResponse->json('data.access_token');

    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->getJson('/tenant/api/user/list');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'users' => [
                    '*' => [
                        'uuid',
                        'full_name',
                        'fname',
                        'lname',
                        'username',
                        'phone',
                        'email',
                        'address',
                        'status',
                        'role' => [
                            'uuid',
                            'name',
                        ],
                        'position_board' => [
                            'uuid',
                            'title',
                        ],
                        'avatar_url',
                    ],
                ],
            ],
        ]);

    $sharedData['org_token'] = $token;
});

it('fetches permission list successfully when authenticated', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['org_token'], 'Login token is missing!');

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['org_token']}",
    ])->getJson('/tenant/api/permission/list');

    $response->assertStatus(200)
    ->assertJsonStructure([
        'success',
        'message',
        'data' => [
            '*' => [
                'uuid',
                'name',
                'slug',
                'children' => [
                    '*' => [
                        'uuid',
                        'name',
                        'slug',
                    ],
                ],
            ],
        ],
    ]);

    $permissions = $response->json('data');

    $this->assertNotEmpty($permissions, 'Permission list is empty!');

    $randomIndex = array_rand($permissions);
    $randomPermission = $permissions[$randomIndex];
    $randomPermissionUuid = $randomPermission['uuid'];

    $childUuids = [];
    if (!empty($randomPermission['children']) && is_array($randomPermission['children'])) {
        foreach ($randomPermission['children'] as $child) {
            $childUuids[] = $child['uuid'];
        }
    }

    $sharedData['permission_id'] = $randomPermissionUuid;
    $sharedData['child_permission_ids'] = $childUuids;
});

it('Stores roles for the organization admin', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['org_token'], 'Login token is missing!');
    $this->assertNotEmpty($sharedData['child_permission_ids'], 'Permission ID is missing!');

    $faker = Faker::create();
    $data = [
        'uuid' => "",
        'name' => $faker->firstName,
        'permission_ids' => $sharedData['child_permission_ids'],
    ];

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['org_token']}",
    ])->postJson('/tenant/api/role/store', $data);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [],
        ]);
});

it('fetches roles list successfully when authenticated', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['org_token'], 'Login token is missing!');

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['org_token']}",
    ])->getJson('/tenant/api/role/list');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'roles' => [
                    '*' => [
                        'uuid',
                        'name',
                        'slug',
                        'permissions',
                    ],
                ],
            ],
        ]);

    $roles = $response->json('data.roles');
    $this->assertNotEmpty($roles, 'Roles list is empty!');
    $randomIndex = array_rand($roles);
    $randomRole = $roles[$randomIndex];
    $randomRoleUuid = $randomRole['uuid'];
    $randomRoleName = $randomRole['name'];

    $sharedData['role_id'] = $randomRoleUuid;
    $sharedData['name'] = $randomRoleName;
});

it('fetches position board list successfully when authenticated', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['org_token'], 'Login token is missing!');

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['org_token']}",
    ])->getJson('/tenant/api/position-board/list');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'uuid',
                    'title',
                    'slug',
                ],
            ],
        ]);

    $positionBoards = $response->json('data');
    $this->assertNotEmpty($positionBoards, 'Position boards list is empty!');
    $randomIndex = array_rand($positionBoards);
    $randomPositionBoard = $positionBoards[$randomIndex];
    $randomPositionBoardUuid = $randomPositionBoard['uuid'];

    $sharedData['position_board_uuid'] = $randomPositionBoardUuid;
});

it('Stores user for the organization admin', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['org_token'], 'Login token is missing!');
    $this->assertNotEmpty($sharedData['role_id'], 'Role ID is missing!');
    $this->assertNotEmpty($sharedData['position_board_uuid'], 'Position Board ID is missing!');

    $faker = Faker::create();
    $data = [
        'role_id' => $sharedData['role_id'],
        'fname' => $faker->firstName,
        'lname' => $faker->lastName,
        'email' => $faker->safeEmail,
        'phone' => $faker->phoneNumber,
        'address' => $faker->address,
        'position_board_id' => $sharedData['position_board_uuid'],
        'username' => $faker->userName,
        'term_start_date' => $faker->date('Y-m-d', 'now'),
        'term_end_date' => $faker->date('Y-m-d', '+1 year'),
        'status' => '1', 
    ];

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['org_token']}",
    ])->post('/tenant/api/user/store', $data);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [],
        ]);

    $sharedData['user_email'] = $data['email'];
});

it('Updates roles for the organization admin', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['org_token'], 'Login token is missing!');
    $this->assertNotEmpty($sharedData['role_id'], 'Role ID is missing!');
    $this->assertNotEmpty($sharedData['name'], 'Role ID is missing!');
    $this->assertNotEmpty($sharedData['child_permission_ids'], 'Permission ID is missing!');

    $data = [
        'uuid' => $sharedData['role_id'],
        'name' => $sharedData['name'],
        'permission_ids' => $sharedData['child_permission_ids'],
    ];

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['org_token']}",
    ])->postJson('/tenant/api/role/store', $data);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [],
        ]);
});

it('Deletes role for the organization admin', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['org_token'], 'Login token is missing!');
    $this->assertNotEmpty($sharedData['role_id'], 'Role ID is missing!');

    $roleId = $sharedData['role_id'];

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['org_token']}",
    ])->deleteJson("/tenant/api/role/delete/{$roleId}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);
});

it('fetches users list for the organization user uuid', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['org_token'], 'Login token is missing!');

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['org_token']}",
    ])->getJson('/tenant/api/user/list');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'users' => [
                    '*' => [
                        'uuid',
                        'full_name',
                        'fname',
                        'lname',
                        'username',
                        'phone',
                        'email',
                        'address',
                        'status',
                        'role' => [
                            'uuid',
                            'name',
                        ],
                        'position_board' => [
                            'uuid',
                            'title',
                        ],
                        'avatar_url',
                    ],
                ],
            ],
        ]);

    $users = $response->json('data.users');
    $this->assertNotEmpty($users, 'User list is empty!');

    $sharedData['user_uuid'] = $users[0]['uuid'];
    $sharedData['user_email'] = $users[0]['email'];
    $sharedData['user_name'] = $users[0]['username'];
});

it('Updates user for the organization admin', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['org_token'], 'Login token is missing!');
    $this->assertNotEmpty($sharedData['role_id'], 'Role ID is missing!');
    $this->assertNotEmpty($sharedData['position_board_uuid'], 'Position Board ID is missing!');
    $this->assertNotEmpty($sharedData['user_uuid'], 'User uuid is missing!');
    $this->assertNotEmpty($sharedData['user_email'], 'User email is missing!');
    $this->assertNotEmpty($sharedData['user_name'], 'User name is missing!');

    $faker = Faker::create();
    $data = [
        'role_id' => $sharedData['role_id'],
        'fname' => $faker->firstName,
        'lname' => $faker->lastName,
        'email' => $sharedData['user_email'],
        'phone' => $faker->phoneNumber,
        'address' => $faker->address,
        'position_board_id' => $sharedData['position_board_uuid'],
        'username' => $sharedData['user_name'],
        'term_start_date' => $faker->date('Y-m-d', 'now'),
        'term_end_date' => $faker->date('Y-m-d', '+1 year'),
        'status' => '1', 
    ];

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['org_token']}",
    ])->put("/tenant/api/user/update?uuid={$sharedData['user_uuid']}", $data);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [],
        ]);
});

it('Deletes user for the organization admin', function () use (&$sharedData) {
    $this->assertNotEmpty($sharedData['org_token'], 'Login token is missing!');
    $this->assertNotEmpty($sharedData['user_uuid'], 'User uuid is missing!');

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$sharedData['org_token']}",
    ])->delete("/tenant/api/user/delete?uuid[]={$sharedData['user_uuid']}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [],
        ]);
});
