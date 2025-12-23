# Development

If you would like to modify any front-end pages you need have instlled node.js in your computer.

### Install Node.js

You can follow the node.js website(<https://nodejs.org/en/download>) for that

### Install Yarn

```
npm install --global yarn
```

### Watch or Build

After making any changes you need to build or watch front-end.

```
# Watch
yarn watch

# Build Dev
yarn dev

# Build Production
yarn prod
```

### Update/Modify Vue.js Files

After making any changes on the vue.js files, you need to run the above watch or build command.

### Backend Change

After making changes on the web route or clear the cache you need to run the following commands.

```
php artisan optimize
php artisan cache:clear
php artisan route:cache
php artisan view:clear
php artisan config:cache
php artisan route:clear
```

### Update Composer

If you facing any issue on the command/terminal end for the changing PHP version you may need to update the dependancies with running the following command

```
composer update
```
