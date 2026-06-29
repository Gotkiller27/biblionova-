<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public auth routes
    require __DIR__.'/api/auth.php';

    // Protected routes (require auth)
    Route::middleware(['auth:sanctum', 'check.account.status'])->group(function () {
        // Authenticated user routes (auth, profile, document revisions, dashboards)
        require __DIR__.'/api/authenticated.php';

        // User management routes
        require __DIR__.'/api/users.php';

        // Categories routes
        require __DIR__.'/api/categories.php';

        // Authors routes
        require __DIR__.'/api/authors.php';

        // Publishers routes
        require __DIR__.'/api/publishers.php';

        // Keywords routes
        require __DIR__.'/api/keywords.php';

        // References routes
        require __DIR__.'/api/references.php';

        // Favorites routes
        require __DIR__.'/api/favorites.php';

        // Citations routes
        require __DIR__.'/api/citations.php';

        // Deposit requests routes
        require __DIR__.'/api/deposit-requests.php';

        // Statistics routes
        require __DIR__.'/api/statistics.php';

        // Notifications routes
        require __DIR__.'/api/notifications.php';
    });
});
