<?php

namespace App\Models;

use App\Models\Contracts\BaseModel;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkLog extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var  array
     */
    protected $fillable = [
        'developer_id',
        'project_id',
        'rate',
        'hrs',
        'total',
        'status',
        'date'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var  array
     */
    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime:Y-m-d',
        'date' => 'datetime:Y-m-d',
    ];

    /**
     * Returns rate based on developer's project's or client's
     * @param  $developer_id
     * @param  $project_id
     * @return  float
     */
    public static function GetRate($developer_id, $project_id): float
    {
        if ($developer_id) {
            $developer = Developer::find($developer_id);
            if ($developer->rate != '0.00') {
                return floatval($developer->rate);
            }
        }
        if ($project_id) {
            $project = Project::find($project_id);
            if ($project->rate != '0.00') {
                return floatval($project->rate);
            }
        }
        if ($project_id) {
            $client = Project::find($project_id)->client;
            if ($client->rate != '0.00') {
                return floatval($client->rate);
            }
        }
        return 0;
    }

    /**
     * Get the developer that works.
     */
    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    /**
     * Get the project.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Checks if developer will work more than 24 hours/day
     * @param  $query
     * @param  string  $developer_id
     * @param  float  $hrs
     * @param  string  $id
     * @param  string  $created_at
     * @return  float
     * @throws  ValidationException
     */
    public function scopeCheckMaxHoursToday($query, string $developer_id, float $hrs, string $created_at = '', string $id = ''): float
    {
        if ($created_at == '') {
            $currentDate = Carbon::now()->format('Y-m-d');
        } else {
            $currentDate = $created_at;
        }

        $totalHours = $query->where('developer_id', $developer_id)
            ->whereDate('date', $currentDate)
            ->where('id', '<>', $id)
            ->sum('hrs');

        if ($totalHours + $hrs > 24) {
            throw ValidationException::withMessages(['hrs' => 'Developer must not work more than 24 hours/day']);
        }
        return $totalHours;
    }


    /**
     * Sum of payed worklogs between dates
     * @param  $query
     * @param  DateTimeInterface  $start
     * @param  DateTimeInterface  $end
     * @return mixed
     */
    public function scopeTotalPayed($query, DateTimeInterface $start, DateTimeInterface $end): mixed
    {
        return $query->where('status', true)
            ->whereBetween('date', [$start, $end])
            ->sum('total');
    }

    /**
     * Sum of unpayed worklogs between dates
     * @param  $query
     * @param  DateTimeInterface  $start
     * @param  DateTimeInterface  $end
     * @return  mixed
     */
    public function scopeTotalUnpayed($query, DateTimeInterface $start, DateTimeInterface $end): mixed
    {
        return $query->where('status', false)
            ->whereBetween('date', [$start, $end])
            ->sum('total');
    }

    /**
     * Returns array of values for selecting in filter
     * @return  array
     */
    public static function GetFilterData(): array
    {
        $worklog = new WorkLog();
        $columns = $worklog->getConnection()->getSchemaBuilder()->getColumnListing($worklog->getTable());
        $columns = array_diff($columns, ['created_at', 'updated_at']);

        $filterData = [];
        foreach ($columns as $column) {
            $filterData[$column] = WorkLog::distinct()->orderBy($column)->pluck($column)->toArray();
        }
        $filterData['date'] = array_map(function ($item) {
            return \Carbon\Carbon::parse($item)->toDateString();
        }, $filterData['date']);

        $developers = Developer::whereIn('id', $filterData['developer_id'])->orderBy('first_name')->get(DB::raw('id, CONCAT(first_name, " ", last_name) AS name'));
        unset($filterData['developer_id']);

        $projects = Project::whereIn('id', $filterData['project_id'])->orderBy('name')->get(['name', 'id']);
        unset($filterData['project_id']);

        $filterData['developers'] = $developers;
        $filterData['projects'] = $projects;
        return $filterData;
    }
}
