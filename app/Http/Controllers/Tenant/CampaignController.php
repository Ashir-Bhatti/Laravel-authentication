<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Campaign;
use App\Http\Requests\Tenant\CampaignRequest;
use App\Http\Resources\Tenant\CampaignCollection;

use App\Repositories\Tenant\CampaignRepository;

class CampaignController extends Controller
{
    function __construct(private CampaignRepository $repository) {}

    public function index() 
    {
        return json_response(
            200,
            "Campaigns fetched successfully",
            CampaignCollection::make(Campaign::search(request('search'))->paginate(request('rowsPerPage', 20)))
        );
    }

    public function store(CampaignRequest $request) 
    {
        return $this->repository->store($request->all());
    }

    public function delete(CampaignRequest $request) 
    {
        return json_response(200, "Campaign deleted successfully.", $this->repository->destroyMultiple($request->uuid));
    }
}
