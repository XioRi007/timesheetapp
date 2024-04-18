<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\Developer;
use App\Models\Project;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Form;
use Tests\Browser\Traits\FilteringTrait;
use Tests\Browser\Traits\PaginationTrait;
use Tests\Browser\Traits\SortingTrait;
use Tests\DuskTestCase;

class DeveloperWorkLogsTest extends DuskTestCase
{
    use DatabaseTruncation, SortingTrait, FilteringTrait, PaginationTrait;
    public function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->userId = $user->id;
        $this->pageClass = 'Tests\Browser\Pages\DeveloperWorkLogsPage';
        $client = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id
        ]);
        $developer = Developer::factory()->create();
        WorkLog::factory()->create([
            'project_id' => $project->id,
            'developer_id' => $developer->id,
        ]);
    }
}
