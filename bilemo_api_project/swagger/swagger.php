<?php

use OpenApi\Annotations as OA;

define("API_HOST", ($env === "production") ? "example.com" : "localhost");

/**
 * @OA\Info(title="Api BileMo", version="1")
 * @OA\Server(
 *      url="/api/v1",
 *      description="Api BileMo"
 * )
 * 
 */
