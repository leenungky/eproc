## konfigurasi queue
a. Migrate Queue Table 

    php artisan queue:table  # creates a table for queued jobs
    php artisan migrate # migrates the table 

b. set .env

    QUEUE_CONNECTION=database

c. testing queue di local

    php artisan queue:work --sleep=3 --tries=3

## konfigurasi supervisor untuk mengontrol queue worker
   
a. Install supervisor 

Ubuntu : ```apt-get install -y supervisor```

centos : [https://cloudwafer.com/blog/how-to-install-and-configure-supervisor-on-centos-7/](https://cloudwafer.com/blog/how-to-install-and-configure-supervisor-on-centos-7/)

b. Tambah program [program:eproc-worker] `vim /etc/supervisor/conf.d/supervisord.conf`

    [program:eproc-worker]
    process_name=%(program_name)s_%(process_num)02d
    command=php /var/www/html/eproc/artisan queue:work --sleep=3 --tries=3
    autostart=true
    autorestart=true
    user=forge
    numprocs=8
    redirect_stderr=true
    stdout_logfile=/var/www/html/eproc/storage/logs/worker.log
    stopwaitsecs=3600

c. Perintah-perintah supervisor

    #untuk menjalankan supervisor
    supervisord -c /etc/supervisor/supervisord.conf
    #service supervisor start
    service supervisor stop
    service supervisor restart

    #supervisorctl untuk cek status, start dan stop kafka consume
    supervisorctl status
    supervisorctl start eproc-worker:*
    supervisorctl stop eproc-worker:*
# eproc
