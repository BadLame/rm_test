<?php

namespace App\Models\Enums\User;

enum SiteModulesAccessEnum: string
{
    case ADMINISTRATIVE = 'administrative';
    case ANALYTICS = 'analytics';
    case NOTIFICATIONS = 'notifications';
    case FEED = 'feed';
    case TICKETS = 'tickets';
    case REQUESTS = 'requests';
    case PLACES_ON_MAP = 'places_on_map';
}
