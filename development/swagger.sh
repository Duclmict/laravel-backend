#!/bin/bash

mkdir ../public/swagger
php ../vendor/bin/swagger --bootstrap ./swagger-constants.php --output ../public/swagger ./swagger-v1.php ../app/Http/Controllers
cd

/**
 * @SWG\Get(
 *     path="/create",
 *     description="Return a user's first and last name",
 *     @SWG\Parameter(
 *         name="firstname",
 *         in="query",
 *         type="string",
 *         description="Your first name",
 *         required=true,
 *     ),
 *     @SWG\Parameter(
 *         name="lastname",
 *         in="query",
 *         type="string",
 *         description="Your last name",
 *         required=true,
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="OK",
 *     ),
 *     @SWG\Response(
 *         response=422,
 *         description="Missing Data"
 *     )
 * )
 */