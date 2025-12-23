# Update Script

1. Replace all existing files with latest files except `.env` file
2. Go to update link, such as: `http://helpdesk.com/update`

With doing above your helpdesk script would be up to date.

If you still facing any issue you can try to use the following command.(Not required)

```
php artisan migrate
```

To Clear Cache

```
php artisan optimize && php artisan cache:clear && php artisan route:cache && php artisan view:clear && php artisan config:cache && php artisan route:clear
```
