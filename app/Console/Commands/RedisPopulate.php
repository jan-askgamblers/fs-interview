<?php

namespace App\Console\Commands;

use App\Services\RedisPopulator\CasinoPopulator;
use App\Services\RedisPopulator\GamePopulator;
use App\Services\RedisPopulator\RedisPopulatorException;
use Illuminate\Console\Command;

class RedisPopulate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:redis:populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate Redis with Game and Casino data in JSON.';

    /**
     * get every casino and game from the database
     * create a sorted list of games and casinos, in the same JSON format
     * create a sorted list of games and casinos grouped by market, and without market
     * store these sorted structures in redis
     */
    public function handle(): void
    {
        /**
         * With only 2 sets of data, I would not abstract populators further. If there would be more data types, here
         * we should get a collection of populators and make them do their job in a loop.
         */

        $casinoPopulator = new CasinoPopulator();
        try {
            $casinoPopulator->populate();
            $this->info('Done creating Casino sorted sets');

        } catch (RedisPopulatorException $e) {
            $this->error($e->getMessage());
        }

        $gamePopulator = new GamePopulator();
        try {
            $gamePopulator->populate();
            $this->info('Done creating Game sorted sets');

        } catch (RedisPopulatorException $e) {
            $this->error($e->getMessage());
        }
    }
}
