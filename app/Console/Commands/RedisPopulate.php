<?php

namespace App\Console\Commands;

use App\Models\Casino;
use App\Models\Game;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

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
    public function handle()
    {
//        Redis::command('flushall');
//        $this->info('Redis flushed');
//        return;

//        return $this->writeOutSortedSet('casinos-global');

        $casinoQuery = Casino::query();
//        $casinoQuery->orderByDesc('rank'); // no need for ordering in SQL, redis sorted sets will handle this
//        $casinoQuery->limit(100); // for testing

        // iterate over results and create the JSON structure
        foreach ($casinoQuery->get() as $casino) {
            $casinoData = [
                'uuid' => uniqid(),
                'name' => $casino->name,
                'url' => $casino->url,
                'image' => $casino->logo,
                'ordering' => $casino->rank,
                'market' => $casino->market,
                'type' => 'casino'
            ];

            /**
             * adding with rank, which will create a sorted list with ascending ranks, we will use zrevrange to read
             * back in descending order. This way no extra math is needed to calculate scores for the sorted set.
             */
            try {
                Redis::command('zadd', ['casinos-global', $casino->rank, json_encode($casinoData)]);
                Redis::command('zadd', ['casinos-' . $casino->market, $casino->rank, json_encode($casinoData)]);
            } catch (\Exception $e) {
                $this->error($e->getMessage() . ' current Casino: ' . var_dump($casinoData));
            }
        }

        $this->info('Done creating Casino sorted sets');

        $gamesQuery = Game::query();
//        $gamesQuery->limit(10); // for testing

        foreach ($gamesQuery->get() as $game) {
            $gameData = [
                'uuid' => uniqid(),
                'name' => $game->name,
                'url' => $game->link,
                'image' => $game->screenshot,
                'ordering' => $game->numberOfPlays,
                'market' => $game->market,
                'type' => 'game'
            ];

            try {
                Redis::command('zadd', ['games-global', $game->numberOfPlays, json_encode($gameData)]);
                Redis::command('zadd', ['games-' . $game->market, $game->numberOfPlays, json_encode($gameData)]);
            } catch (\Exception $e) {
                $this->error($e->getMessage() . ' current Game: ' . var_dump($gameData));
            }
        }

        $this->info('Done creating Game sorted sets');
    }

    private function writeOutSortedSet($redisKey): void
    {
        $casinos = Redis::command('zrevrange', [$redisKey, 0, -1]);
        foreach ($casinos as $casino) {
            $this->info($casino);
        }
    }
}
