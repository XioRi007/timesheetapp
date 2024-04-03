<?php

namespace App\Models;

use App\Models\Contracts\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var  array
     */
    protected $fillable = [
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
     * Get the projects of the client.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Returns array of values for selecting in filter
     * @return  array
     */
    public static function GetFilterData(): array
    {
        $client = new Client();
        $columns = $client->getConnection()->getSchemaBuilder()->getColumnListing($client->getTable());
        $columns = array_diff($columns, ['created_at', 'updated_at']);

        $filterData = [];
        foreach ($columns as $_column) {
            $filterData[$_column] = Client::distinct()->orderBy($_column)->pluck($_column)->toArray();
        }
        return $filterData;
    }
}
