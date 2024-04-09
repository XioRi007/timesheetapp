<?php

namespace App\Models;

use App\Models\Contracts\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Developer extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'user_id',
        'rate',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Returns developer name and array of hours worked in month
     * @param  $startOfMonth
     * @param  $endOfMonth
     * @return mixed
     */
    public static function getDevelopersWorkLogHoursByMonth($startOfMonth, $endOfMonth)
    {
        $developers = Developer::where('status', true)
            ->select('id', 'first_name', 'last_name')
            ->with('workLogs:hrs,date,id,developer_id')
            ->whereHas('workLogs', function ($query) use ($startOfMonth, $endOfMonth) {
                return $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
            })
            ->paginate(50)
            ->withQueryString()
            ->through(function ($developer, $key) use ($startOfMonth, $endOfMonth) {
                $hours = [];
                for ($date = $startOfMonth->copy(); $date <= $endOfMonth; $date->addDay()) {
                    $totalHours = collect($developer->workLogs)
                        ->filter(function ($workLog) use ($date) {
                            return $workLog['date']->toDateString() === $date->toDateString();
                        })
                        ->sum('hrs');
                    $hours[] = $totalHours;
                }
                $developer['name'] = $developer->full_name;
                $developer['hours'] = $hours;
                unset($developer['first_name']);
                unset($developer['last_name']);
                unset($developer->workLogs);
                return $developer;
            });
        return $developers;
    }

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the work logs of the developer.
     */
    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class);
    }

    /**
     * Get the full name of the developer.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Returns array of values for selecting in filter
     * @return  array
     */
    public static function GetFilterData(): array
    {
        $developer = new Developer();
        $columns = $developer->getConnection()->getSchemaBuilder()->getColumnListing($developer->getTable());
        $columns = array_diff($columns, ['created_at', 'updated_at']);

        $filterData = [];
        foreach ($columns as $column) {
            $filterData[$column] = Developer::distinct()->orderBy($column)->pluck($column)->toArray();
        }
        return $filterData;
    }
}
