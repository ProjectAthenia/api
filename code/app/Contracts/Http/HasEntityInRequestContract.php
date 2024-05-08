<?php
declare(strict_types=1);

namespace App\Contracts\Http;

use App\Contracts\Models\IsAnEntityContract;
use Illuminate\Routing\Route;

/**
 * Interface IsEntityRequestContract
 * @package App\Contracts\Http
 */
interface HasEntityInRequestContract
{
    /**
     * Gets the entity out of the route. It will almost always be the first object.
     *
     * @return IsAnEntityContract|Route|object|string
     */
    public function getEntity(): IsAnEntityContract;
}