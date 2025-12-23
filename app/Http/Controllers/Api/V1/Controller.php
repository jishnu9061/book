<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "HelpDesk Pro API",
    description: "RESTful API for HelpDesk Pro - Complete help desk management system"
)]
#[OA\Server(
    url: "/api/v1",
    description: "API v1 Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    name: "Authorization",
    in: "header",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
#[OA\Tag(
    name: "Authentication",
    description: "User authentication endpoints"
)]
#[OA\Tag(
    name: "Tickets",
    description: "Ticket management endpoints"
)]
#[OA\Tag(
    name: "Contacts",
    description: "Contact management endpoints"
)]
#[OA\Tag(
    name: "Users",
    description: "User management endpoints"
)]
#[OA\Tag(
    name: "Dashboard",
    description: "Dashboard and analytics endpoints"
)]
class Controller
{
    //
}

