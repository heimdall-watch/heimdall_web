<?php


namespace App\Command;

use Rewieer\TaskSchedulerBundle\Task\AbstractScheduledTask;
use Rewieer\TaskSchedulerBundle\Task\Schedule;

/**
 * Class ProcessScheduledTasks
 * @package App\Command
 *
 * VERY IMPORTANT
 * TO MAKE THIS WORK YOU NEED TO MAKE THE FOLLOWING COMMANDS :
 *
 * crontab -e
 *      * * * * * docker exec -ti -u heimdall heimdall_web /home/www/heimdall_web/bin/console ts:run >> ~/test.txt
 *
 * This last line will able the everyMinutes in the initialize
 * every * represents a time unit
 * see http://hardware-libre.fr/2014/03/8-exemples-pour-maitriser-linux-cron/ to read more about crontab

 * You will of course need to run the containers first.
 */
class ProcessScheduledTasks extends AbstractScheduledTask
{
    protected function initialize(Schedule $schedule) {
        $schedule
            ->everyMinutes(1);
    }

    public function run() {
        shell_exec('python3 /home/www/heimdall_calendar/icsCalendar.py');
    }
}