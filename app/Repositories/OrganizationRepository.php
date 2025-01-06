<?php

namespace App\Repositories;

use App\Mail\OnBoardingEmail;
use App\Models\{File, User, Organization, Subscription, SubscriptionDetail};
use App\Models\Tenant\Role;
use App\Models\Tenant\User as TenantUser;
use Illuminate\Support\Facades\{ DB, Hash, Log, Mail };
use Illuminate\Support\Str;

use function Illuminate\Log\log;

class OrganizationRepository extends BaseRepository
{
    public function __construct(Organization $model)
    {
        $this->model = $model;
    }

    function store($attributes, $newTenant)
    {
        $sub = Subscription::findByUUIDOrFail($attributes['subscription']['subscription_id']);
        $organization = null ;
        try {
            DB::transaction(function () use ($newTenant, $attributes, &$organization, $sub) {
                $organization = Organization::create([
                    'tenant_id' => $newTenant->id,
                    'title' => $attributes['title'],
                    'registration_number' => $attributes['registration_number'],
                    'description' => $attributes['description'],
                    'address' => $attributes['address'],
                    'city' => $attributes['city'],
                    'state' => $attributes['state'],
                    'phone' => $attributes['phone'],
                    'zip' => $attributes['zip'],
                    'status' => $attributes['status'],
                ]);
            
                $file = new File();
                if(isset($attributes['avatar'])){
                    $type = 'organization_avatar';
                    $file = $file->createImage($attributes['avatar'], $type);
                    if ($file) {
                        $organization->files()->attach([$file->id]);
                    }
                }

                //Subscription Details
                $subscriptionInfo = $attributes['subscription'];
                SubscriptionDetail::create([
                    "organization_id" => $organization->id,
                    "subscription_id" => $sub->id,
                    "billing_start_date" => date("Y-m-d H:i:s", strtotime($subscriptionInfo['billing_start_date'])),
                    "setup_fee" => $subscriptionInfo['setup_fee'],
                    "setup_fee_start_date" => date("Y-m-d H:i:s" , strtotime($subscriptionInfo['setup_fee_start_date'])),
                    "total" => $sub->price_per_license + $subscriptionInfo['setup_fee'],
                ]);

                tenancy()->initialize($newTenant);

                $role = Role::where("slug", "owner")->firstOrFail();

                // Create Tenant User
                $adminInfo = $attributes['admin_info'];
                $tenantUser = TenantUser::create([
                    'fname'     => $adminInfo['fname'],
                    'lname'     => $adminInfo['lname'],
                    'username'  => $adminInfo['username'],
                    'phone'     => $adminInfo['phone'],
                    'email'     => $adminInfo['email'],
                ]);

                // Assign Role to Tenant User
                $tenantUser->roles()->attach($role->id);

                // End tenancy before creating the global user
                tenancy()->end();

                // $password = Str::random(12);
                $password = 'password';
                $user = User::create([
                    'username'        => $tenantUser->username,
                    'email'           => $tenantUser->email,
                    'password'        => Hash::make($password),
                    'phone'           => $tenantUser->phone,
                    'tenant_user_id'  => $tenantUser->id,
                    'tenant_id'       => $newTenant->id,
                    'organization_id' => $organization->id,
                    'role'            => $role->slug ?? 'company_admin',
                ]);
    
                Mail::to($user->email)->send(new OnBoardingEmail($user->email, $password));
            });
            return json_response(200, "Organization Created Successfully", $organization);
        } catch (\Exception $e){
            return json_response(500, "An error occurred while creating the organization. Please try again.");
        }
    }

    function updateStatus(array $attributes = [])
    {
        $organization = Organization::findByUUIDOrFail($attributes['organization_id']);
        $subDetail = SubscriptionDetail::where('organization_id', $organization->id)->first();

        if ($subDetail && $subDetail->status === $attributes['status']) {
            return json_response(403, "Unable to change the status against current request");
        }

        if ($attributes['status'] === 'pause') {
            if (empty($attributes['pause_start_date'])) {
                return json_response(403, "Pause Start Date and pause weeks are required.");
            }

            $subDetail->pause_start_date = date('Y-m-d', strtotime($attributes['pause_start_date']));
            $subDetail->pause_subscription_months = $attributes['pause_weeks'] * 7;
        } else {
            $subDetail->pause_start_date = null;
            $subDetail->pause_subscription_months = null;
        }

        $subDetail->status = $attributes['status'];
        $subDetail->save();

        return json_response(200, 'Status updated successfully');
    }

    function update($attributes, $auth)
    {
        $organization = null;
        $organizationId = $auth->organization_id;
        $tenantUserId = $auth->tenant_user_id;
        $tenantId = $auth->tenant_id;
        try {
            DB::transaction(function () use ($attributes, &$organization, $organizationId, $tenantUserId, $tenantId) {
                $organization = Organization::findOrFail($organizationId);

                $organization->update([
                    'description' => $attributes['description'],
                    'address' => $attributes['address'],
                    'city' => $attributes['city'],
                    'state' => $attributes['state'],
                    'phone' => $attributes['phone'],
                    'zip' => $attributes['zip'],
                ]);

                tenancy()->initialize($tenantId);

                // Update Tenant User
                $adminInfo = $attributes['admin_info'];
                $tenantUser = TenantUser::findOrFail($tenantUserId);
                $tenantUser->update([
                    'fname'     => $adminInfo['fname'],
                    'lname'     => $adminInfo['lname'],
                    'phone'     => $adminInfo['phone'],
                    'address'   => $adminInfo['address'],
                ]);

                tenancy()->end();

                $user = User::where('tenant_user_id', $tenantUserId)->firstOrFail();
                $user->update([
                    'phone' => $tenantUser->phone,
                ]);
            });
            return json_response(200, "Organization Updated Successfully", $organization);
        } catch (\Exception $e){
            return json_response(500, "An error occurred while creating the organization. Please try again.");
        }
    }
}
