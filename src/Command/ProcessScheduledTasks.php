<?php


namespace App\Command;

use Rewieer\TaskSchedulerBundle\Task\AbstractScheduledTask;
use Rewieer\TaskSchedulerBundle\Task\Schedule;

class ProcessScheduledTasks extends AbstractScheduledTask
{
    protected function initialize(Schedule $schedule) {
        $schedule
            ->everyMinutes(5); // Perform the task every 5 minutes
    }

    public function run() {
        // Do suff
    }
}