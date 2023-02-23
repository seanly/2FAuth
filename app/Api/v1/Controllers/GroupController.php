<?php

namespace App\Api\v1\Controllers;

use App\Api\v1\Requests\GroupAssignRequest;
use App\Api\v1\Requests\GroupStoreRequest;
use App\Api\v1\Resources\GroupResource;
use App\Api\v1\Resources\TwoFAccountCollection;
use App\Facades\Groups;
use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $groups = Groups::prependTheAllGroup($request->user()->groups, $request->user()->id);

        return GroupResource::collection($groups);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Api\v1\Requests\GroupStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(GroupStoreRequest $request)
    {
        $this->authorize('create', Group::class);

        $validated = $request->validated();

        $group = $request->user()->groups()->create($validated);

        return (new GroupResource($group))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \App\Api\v1\Resources\GroupResource
     */
    public function show(Group $group)
    {
        $this->authorize('view', $group);

        return new GroupResource($group);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Api\v1\Requests\GroupStoreRequest  $request
     * @param  \App\Models\Group  $group
     * @return \App\Api\v1\Resources\GroupResource
     */
    public function update(GroupStoreRequest $request, Group $group)
    {
        $this->authorize('update', $group);

        $validated = $request->validated();

        Groups::update($group, $validated);

        return new GroupResource($group);
    }

    /**
     * Associate the specified accounts with the group
     *
     * @param  \App\Api\v1\Requests\GroupAssignRequest  $request
     * @param  \App\Models\Group  $group
     * @return \App\Api\v1\Resources\GroupResource
     */
    public function assignAccounts(GroupAssignRequest $request, Group $group)
    {
        $this->authorize('update', $group);

        $validated = $request->validated();

        Groups::assign($validated['ids'], $group);

        return new GroupResource($group);
    }

    /**
     * Get accounts assigned to the group
     *
     * @param  \App\Models\Group  $group
     * @return \App\Api\v1\Resources\TwoFAccountCollection
     */
    public function accounts(Group $group)
    {
        $this->authorize('view', $group);

        return new TwoFAccountCollection($group->twofaccounts());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);

        Groups::delete($group->id);

        return response()->json(null, 204);
    }
}
