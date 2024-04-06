<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Client::class, 'client');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = $this->ParseQuery($request);
        $filterParams = $query['filterParams'];
        $column = $query['column'];
        $ascending = $query['ascending'];
        $clients = Client::filter($filterParams)
            ->sort($column, $ascending)
            ->paginate(50, ['id', 'name', 'rate', 'status'])
            ->withQueryString();

        $filterData = Client::GetFilterData();

        return Inertia::render('Client/Index', [
            'clients' => $clients,
            'filterParams' => $filterParams,
            'column' => $column,
            'ascending' => $ascending == 'asc',
            'filterData' => $filterData,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        Client::create($request->validated());
        return redirect(route('clients.index'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Client/Create', [
            'backLink' => $request->header('referer')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): Client
    {
        return $client;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Client $client): Response
    {
        return Inertia::render('Client/Edit', [
            'client' => $client,
            'backLink' => $request->header('referer')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());
        return redirect(route('clients.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): void
    {
        try {
            $client->delete();
        } catch (Throwable $e) {
            if ($e->getCode() == 23000) {
                throw new Exception('You cannot delete client with projects');
            } else {
                throw $e;
            }
        }
    }
}
