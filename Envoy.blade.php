@servers(['production' => ['runcloud@hidemail.glibix.com']])

@setup
    $repository = 'git@bitbucket.org:mathewparet/hide-mail.git';
    $app_dir = '/home/runcloud/webapps/app-hidemail';
@endsetup

@story('destroy')
    destroy_server
    warn_destroy
@endstory

@task('warn_destroy')
    echo "The below app WILL be deleted:"
    echo $repository
    echo $app_dir
@endtask

@task('destroy_server', ['confirm' => true])
    echo "Destroying server..."
    cd {{ $app_dir }}
    rm -rf .git/
    rm -rf .git*
    rm -rf .editorconfig
    rm -rf .env.example
    mkdir public
    chmod 755 public
    echo "Done"
@endtask

@story('fresh')
    check_params
    fresh_clone
    run_composer
    configure_environment
    generate_app_key
    run_migrations
    cache_app
    link_storage
    install_passport
    finished
@endstory

@story('deploy')
    enable_maintenance_mode
    clone_repo
    run_composer
    run_migrations
    cache_app
    manage_queue
    disable_maintenance_mode
    finished
@endstory

@task('link_storage')
    echo "Linking storage..."
    cd {{ $app_dir }}
    php artisan storage:link
@endtask

@task('install_passport')
    echo "Installing passport..."
    cd {{ $app_dir }}
    php artisan passport:install --force
@endtask

@task('check_params')
    @if(!$database_name || !$database_user || !$database_password)
        echo "Missing parameters"
        echo "The below parameters were provided: "
        echo "database_name: {{$database_name}}"
        echo "database_user: {{$database_user}}"
        @if($database_password)
            echo "database_password: HIDDEN"
        @else
            echo "database_password: NOT PROVIDED"
        @endif
        exit -1
    @endif
    echo "Params check passed :)"
@endtask

@task('generate_app_key')
    echo "Generating application key..."
    cd {{ $app_dir }}
    php artisan key:generate
@endtask

@task('fresh_clone', ['confirm' => true])
    echo "Downloading code..."
    cd {{ $app_dir }}
    rm -rf public
    git clone -q {{ $repository }} ./
    git checkout master
    chmod 755 public
@endtask

@task('configure_environment')
    echo "Configuring environment..."
    cd {{ $app_dir }}
    cp .env.example .env
    sed -i "s/DB_DATABASE=homestead/DB_DATABASE={{$database_name}}/g" .env
    sed -i "s/DB_USERNAME=homestead/DB_USERNAME={{$database_user}}/g" .env
    sed -i "s/DB_PASSWORD=secret/DB_PASSWORD={{$database_password}}/g" .env
@endtask

@task('clone_repo')
    echo 'Updating code...'
    cd {{ $app_dir }}
    git checkout master
    git pull
    git checkout master
@endtask

@task('run_composer')
    echo "Starting deployment..."
    cd {{ $app_dir }}
    composer install -q
@endtask

@task('run_migrations')
    echo "Running migrations..."
    cd {{ $app_dir }}
    php artisan migrate --force
@endtask

@task('cache_app')
    echo "Caching application..."
    cd {{ $app_dir }}
    php artisan cache:clear
    php artisan config:cache
    php artisan view:cache
@endtask

@task('manage_queue')
    echo "Restarting Queues..."
    cd {{ $app_dir }}
    php artisan queue:restart
@endtask

@task('enable_maintenance_mode')
    echo "Putting application in maintenance mode..."
    cd {{ $app_dir }}
    @if($message)
        php artisan down --message="{{$message}}"
    @else
        php artisan down
    @endif
@endtask

@task('disable_maintenance_mode')
    echo "Activating application..."
    cd {{ $app_dir }}
    php artisan up
@endtask

@task('finished')
    echo "Action completed. If this is a fresh install, you may need to manually configure email.";
@endtask