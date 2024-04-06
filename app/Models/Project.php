<?php

namespace App\Models;

use App\Models\Contracts\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var  array
     */
    protected $fillable = [
        'client_id',
        'name',
        'rate',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var  array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the client that owns the project.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the work logs of the project.
     */
    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class);
    }

    /**
     * Returns array of values for selecting in filter
     * @return  array
     */
    public static function GetFilterData(): array
    {
        $project = new Project();
        $columns = $project->getConnection()->getSchemaBuilder()->getColumnListing($project->getTable());
        $columns = array_diff($columns, ['created_at', 'updated_at']);
        $filterData = [];
        foreach ($columns as $column) {
            $filterData[$column] = Project::distinct()->orderBy($column)->pluck($column)->toArray();
        }
        $clients = Client::whereIn('id', $filterData['client_id'])->orderBy('name')->get(['name', 'id']);
        unset($filterData['client_id']);
        $filterData['clients'] = $clients;
        return $filterData;
    }
}
