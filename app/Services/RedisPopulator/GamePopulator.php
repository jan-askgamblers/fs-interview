<?php

namespace App\Services\RedisPopulator;

use App\Models\Game;
use Illuminate\Support\Facades\Redis;

class GamePopulator implements RedisPopulatorInterface
{
    /**
     * @throws RedisPopulatorException
     */
    public function populate(): void
    {
        /**
         * There is no need for ordering in SQL, redis sorted sets will handle this
         */
        $gamesQuery = Game::query();

        /**
         * @var Game $game
         */
        foreach ($gamesQuery->get() as $game) {
            $gameData = $game->toCardData();

            /**
             * adding with rank, which will create a sorted list with ascending ranks, we will use zrevrange to read
             * back in descending order. This way no extra math is needed to calculate scores for the sorted set.
             */
            try {
                Redis::command('zadd', ['games-global', $game->numberOfPlays, json_encode($gameData)]);
                Redis::command('zadd', ['games-' . $game->market, $game->numberOfPlays, json_encode($gameData)]);
            } catch (\Exception $e) {
                throw new RedisPopulatorException($e->getMessage() . ' current Game: ' . json_encode($gameData));
            }
        }

    }
}
