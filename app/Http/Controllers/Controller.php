<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @param  Request  $request
     * @return  array
     */
    protected function ParseQuery(Request $request): array
    {
        if ($request->query->has('filter')) {
            $filterParams = $request->query('filter');
        } else {
            $filterParams = [];
        }
        if ($request->query->has('column') && $request->query('column') != null) {
            $column = $request->query('column');
        } else {
            $column = 'created_at';
        }
        if ($request->query->has('ascending')) {
            $ascending = $request->query('ascending');
            $ascending = $ascending == 'true' ? 'asc' : 'desc';
        } else {
            $ascending = 'desc';
        }
        return [
            'filterParams' => $filterParams,
            'column' => $column,
            'ascending' => $ascending
        ];
    }

}
