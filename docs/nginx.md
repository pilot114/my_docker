
https://nginx.ru/ru/docs/

## Секция main

    user nginx;
    worker_processes auto;

Задаёт приоритет планирования рабочих процессов [-20:20] чем ниже, тем приоритетнее

    worker_priority 0;

ошибки можно писать в файл/память/syslog
уровни: debug, info, notice, warn, error, crit, alert, emerg  
https://nginx.ru/ru/docs/syslog.html  
https://nginx.ru/ru/docs/debugging_log.html#memory

    error_log  /var/log/nginx/error.log warn;
    pid        /var/run/nginx.pid;

установить переменную окружения

    env TEST=test

загрузить динамический модуль

    load_module modules/ngx_mail_module.so;

Использование PCRE JIT способно существенно ускорить обработку регулярных выражений

    pcre_jit on

Задаёт название аппаратного SSL-акселератора

    ssl_engine устройство;

Задаёт именованные пулы потоков, используемые для многопоточной обработки операций
чтения и отправки файлов без блокирования рабочего процесса

    thread_pool default threads=32 max_queue=65536;

Уменьшает разрешение таймеров времени в рабочих процессах

    timer_resolution 100ms;

лимиты на размер ядра и число открытых файлов

    worker_rlimit_core число;
    worker_rlimit_nofile число;

время для плавного завершения

    worker_shutdown_timeout время;



### events - директивы, влияющие на обработку соединений

общее число коннектов = worker_processes * worker_connections
в реальности ограничивается значением worker_rlimit_nofile

    worker_connections 2048;

рабочие процессы будут принимать соединения по очереди (оптимизация для highload)

    accept_mutex on;
    accept_mutex_delay 500ms;

рабочий процесс за один раз будет брать более чем одно новое соединение

    multi_accept on;

https://nginx.ru/ru/docs/events.html
nginx сам выбирает лучший

    use epoll;
