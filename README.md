## What is this?
    It's a service where you bla bla.... 
    `screenshot here`

## Summary of what's been done:
    - Models with migrations: User(already exist), Team, Asset, Tag
    - Relationships:
                    User                Team                Asset                Tag
            User    -                   belongsToMany       hasMany              polymorphic
            Team    belongsToMany       -                   hasMany              -
            Asset   belongsTo           belongsTo           -                    polymorphic
            Tag     polymorphic         -                   polymorphic          -
   
    - php artisan make:migration create_team_user_table
    - setup Sanctum
    - create routs (group, resource) & controllers
    - Factories
    - user can register, login & logout
    - search assets: search scope, resources
    - user can create/update assets,
    - admin can create users, teams & tags
    - admin can update teams & tags
    - admin can delete tags & teams (pivot record also deleted)
    - an Auth policy
    - a BaseController for json success/error responses
    - resource/collection for all models
    - custom requests $request->validated()
    - 18 tests written
    - Implement Swagger for API documentation

## How To Install:
    - Connect Database
    - Run `php artisan migrate:fresh --seed`
    - Swagger: http://127.0.0.1:8000/api/documentation

## To Do:
    - 

## To Consider:
    - Subscriptions

