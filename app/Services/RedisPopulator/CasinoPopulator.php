<?php

namespace App\Services\RedisPopulator;

use App\Models\Casino;
use Illuminate\Support\Facades\Redis;

class CasinoPopulator implements RedisPopulatorInterface
{
    /**
     * @throws RedisPopulatorException
     */
    public function populate(): void
    {
        /**
         * There is no need for ordering in SQL, redis sorted sets will handle this
         */
        $casinoQuery = Casino::query();

        /**
         * @var Casino $casino
         */
        foreach ($casinoQuery->get() as $casino) {
            $casinoData = $casino->toCardData();

            /**
             * adding with rank, which will create a sorted list with ascending ranks, we will use zrevrange to read
             * back in descending order. This way no extra math is needed to calculate scores for the sorted set.
             */
            try {
                Redis::command('zadd', ['casinos-global', $casino->rank, json_encode($casinoData)]);
                Redis::command('zadd', ['casinos-' . $casino->market, $casino->rank, json_encode($casinoData)]);
            } catch (\Exception $e) {
                throw new RedisPopulatorException($e->getMessage() . ' current Casino: ' . json_encode($casinoData));
            }
        }
    }
}
