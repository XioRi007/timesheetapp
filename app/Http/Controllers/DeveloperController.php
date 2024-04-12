<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeveloperRequest;
use App\Http\Requests\UpdateDeveloperRequest;
use App\Models\Developer;
use App\Models\Project;
use App\Models\User;
use App\Models\WorkLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Throwable;

class DeveloperController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Developer::class, 'developer');
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
        $developers = Developer::filter($filterParams)
            ->sort($column, $ascending)
            ->paginate(50, ['first_name', 'last_name', 'rate', 'status', 'id'])
            ->withQueryString();

        $filterData = Developer::GetFilterData();

        return Inertia::render('Developer/Index', [
            'developers' => $developers,
            'filterParams' => $filterParams,
            'column' => $column,
            'ascending' => $ascending == 'asc',
            'filterData' => $filterData,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeveloperRequest $request)
    {
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole('developer');
        $data = $request->except(['password', 'email']);
        $data['user_id'] = $user->id;
        Developer::create($data);
        return redirect(route('developers.index'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Inertia::render('Developer/Create', [
            'backLink' => $request->header('referer')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function worklogs(Request $request, Developer $developer)
    {
        $query = $this->ParseQuery($request);
        $filterParams = $query['filterParams'];
        $column = $query['column'];
        $ascending = $query['ascending'];
        $projects = Project::all('id', 'name');
        $workLogs = WorkLog::where('developer_id', $developer->id)
            ->with('project:id,name')
            ->filter($filterParams)
            ->sort($column, $ascending)
            ->paginate(50, ['date', 'project_id as project.name', 'project_id', 'rate', 'hrs', 'total', 'status', 'id'])
            ->withQueryString()
            ->through(function ($log, $key) {
                $log['project.name'] = $log->project->name;
                unset($log['project_id']);
                unset($log['project']);
                return $log;
            });
        $filterParams['developer_id'] = $developer->id;
        $filterData = WorkLog::GetFilterData();
        return Inertia::render('Developer/WorkLogs', [
            'worklogs' => $workLogs,
            'filterParams' => $filterParams,
            'projects' => $projects,
            'column' => $column,
            'ascending' => $ascending == 'asc',
            'filterData' => $filterData
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Developer $developer)
    {
        return Inertia::render('Developer/Edit', [
            'developer' => $developer,
            'backLink' => $request->header('referer')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeveloperRequest $request, Developer $developer)
    {
        $developer->update($request->validated());
        return redirect(route('developers.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Developer $developer)
    {
        try {
            $developer->delete();
        } catch (Throwable $e) {
            if ($e->getCode() == 23000) {
                throw new Exception('You cannot delete developer with work logs');
            } else {
                throw $e;
            }
        }
    }
}
