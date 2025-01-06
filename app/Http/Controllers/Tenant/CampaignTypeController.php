<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CampaignTypeRequest;
use App\Http\Resources\Tenant\CampaignTypeCollection;
use App\Models\Tenant\CampaignType;
use App\Repositories\Tenant\CampaignTypeRepository;
use Illuminate\Http\Request;

class CampaignTypeController extends Controller
{
    function __construct(private CampaignTypeRepository $repository) {}

    public function index()
    {
        return json_response(
            200,
            "Campaign types are fetched from database.",
            CampaignTypeCollection::make(CampaignType::search(request('search'))->paginate(request('rowsPerPage', 20)))
        );
    }

    public function store(CampaignTypeRequest $request)
    {
        return $this->repository->store($request->all());
    }

    public function delete(CampaignTypeRequest $request)
    {
        return json_response(200, "Department deleted successfully.", $this->repository->destroyMultiple($request->uuid));
    }
}
