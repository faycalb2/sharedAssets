## Progress:
    (week 1)
    - Models with migrations: User(already exist), Team, Asset, Tag
    - Relationships:
                    User                Team                Asset                Tag
            User    -                   belongsToMany       hasMany              hasMany
            Team    belongsToMany       -                   hasMany              -
            Asset   belongsTo           belongsTo           -                    belongsTo
            Tag     belongsTo           -                   hasMany              -
   
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
    
    (week 2)
    - move auth code to Auth policy
    - move json success/error responses to a BaseController
    - resource/collection for all models
    - change $request->validated($request->all()) to $validated = $request->validated()
    - 18 tests written

## To Do:
    - Write how to install