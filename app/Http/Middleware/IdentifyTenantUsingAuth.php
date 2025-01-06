<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Stancl\Tenancy\Middleware\IdentificationMiddleware;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Tenancy;
use App\Models\Tenant;

class IdentifyTenantUsingAuth extends IdentificationMiddleware
{
	/** @var callable|null */
	public static $onFail;

	/** @var Tenancy */
	protected $tenancy;

	/** @var DomainTenantResolver */
	protected $resolver;

	public function __construct(Tenancy $tenancy, DomainTenantResolver $resolver)
	{
		$this->tenancy = $tenancy;
		$this->resolver = $resolver;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (!request()->user()) {
			return response(['user'=> 'unauthorized'], 401);
		}

		$tenant = Tenant::find(request()->user()->tenant_id);

		if (!$tenant) {
			return response(['Tenant'=> 'Not Found'], 404);
		}

		return $this->initializeTenancy(
			$request, $next, $tenant->id . "." . $request->getHost()
		);
		/*return $this->initializeTenancy(
			$request, $next, session()->get("_tenant_id") . ".localhost"
		);*/
	}
}
