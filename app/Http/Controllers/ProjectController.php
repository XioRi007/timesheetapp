<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Client;
use App\Models\Project;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->ParseQuery($request);
        $filterParams = $query['filterParams'];
        $column = $query['column'];
        $ascending = $query['ascending'];
        $projects = Project::with('client:id,name')
            ->filter($filterParams)
            ->sort($column, $ascending)
            ->paginate(50, ['name', 'client_id as client.name', 'client_id', 'rate', 'status', 'id'])
            ->withQueryString()
            ->through(function ($project) {
                $project['client.name'] = $project->client->name;
                unset($project['client_id'], $project['client']);
                return $project;
            });

        $filterData = Project::GetFilterData();

        return Inertia::render('Project/Index', [
            'projects' => $projects,
            'filterParams' => $filterParams,
            'column' => $column,
            'ascending' => $ascending == 'asc',
            'filterData' => $filterData,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Project::create($request->validated());
        return redirect(route('projects.index'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clients = Client::all('id', 'name');
        return Inertia::render('Project/Create', [
            'clients' => $clients,
            'backLink' => $request->header('referer')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return $project;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Project $project)
    {
        $clients = Client::all('id', 'name');
        return Inertia::render('Project/Edit', [
            'project' => $project,
            'clients' => $clients,
            'backLink' => $request->header('referer')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());
        return redirect(route('projects.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        try {
            $project->delete();
        } catch (Throwable $e) {
            if ($e->getCode() == 23000) {
                throw new Exception('You cannot delete project with work logs');
            } else {
                throw $e;
            }
        }
    }
}
