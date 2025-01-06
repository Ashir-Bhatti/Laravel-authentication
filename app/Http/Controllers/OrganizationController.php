<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationRequest;
use App\Http\Resources\OrganizationCollection;
use App\Models\{Organization, Tenant, Subscription};
use App\Repositories\OrganizationRepository;

class OrganizationController extends Controller
{
    function __construct(private OrganizationRepository $repository){}

    function store(OrganizationRequest $request)
    {
        $name = str_replace(' ', '_', $request->title);
		try {
            $newTenant = new Tenant();
            $newTenant->id = $name;
			if($newTenant->save()) {
				$newTenant->domains()->create([
					'domain' => $name.'.'.$request->getHost()
				]);
			}
		} catch (\Exception $e){
			$tenant = Tenant::find($name);
			if($tenant) {
				$tenant->domains()->delete();
				$tenant->delete();
			}
			return json_response(500, "Organization already exists.");
		}

        $attributes = $request->all();
        if ($request->hasFile('avatar')) {
            $attributes['avatar'] = $request->file('avatar');
        }

        return $this->repository->store($attributes, $newTenant);
    }

    function index()
    {
		return json_response(
            200,
            "Organization Listing fetched from database.",
            OrganizationCollection::make(Organization::search(request('search'))->paginate(request('rowsPerPage')))
        );
    }

    function subscription()
    {
		return json_response(
            200,
            "Subscription Listing fetched from database.",
            Subscription::all()
        );
    }

	function updateStatus(OrganizationRequest $request)
	{
		$auth = request()->user();
        if ($auth->role === 'super_admin' || $auth->provision_account_id > 0) {
            return $this->repository->updateStatus($request->all());
        }
        return json_response(401, "You are not authorized to update.");
	}

    function updateOrganization(OrganizationRequest $request)
	{
        $auth = request()->user();
        if ($auth->role !== 'company_admin') {
            return json_response(401, "You are not authorized to update.");
        }

        $attributes = $request->all();
        return $this->repository->update($attributes, $auth);
	}
}
