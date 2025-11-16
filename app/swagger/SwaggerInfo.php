<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     title="Library Management API",
 *     version="1.0.0",
 *     description="API documentation for Library Management System"
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 * )
 */
class SwaggerInfo
{
    // This class is only used for annotations
}
