count / sizeof - получить размер массива
array_keys / array_key_first / array_key_last - получение ключей
array_values - получение значений

array_push - добавление элементов в конец массива (лучше использовать $arr[] = ... если нужно добавить 1 элемент)
array_pop - извлечь последний элемент
array_shift - извлечь первый элемент
array_unshift - вставить первый элемент
array_splice - удалить или заменить часть массива
array_slice - выбрать часть массива

in_array - проверка наличия в массиве
array_search - нахождение ключа по значению (до первого совпадения)
array_key_exists / key_exists - проверка наличия ключа (индекса)
array_chunk - разбивка на части
array_combine — Создаёт новый массив, используя один массив в качестве ключей, а другой для его значений
extract /compact - распаковка и упаковка переменных в массив
array_fill / array_fill_keys / range - разные заполения массива
array_pad — Дополнить массив определённым значением до указанной длины

https://www.php.net/manual/ru/array.sorting.php
krsort / ksort - сортировка по ключам в порядке убывания / возрастания
natsort / natcasesort - натуральная сортировка и регистронезависимая натуральная сортировка
rsort / sort - сортировка в порядке убывания / возрастания
arsort / asort - сортировка в порядке убывания / возрастания с сохранением ключей
usort / uasort / uksort - для пользовательской функции
shuffle - случайная сортировка
array_multisort - мультисортировка (как в БД)

min / max - выбор значения из массива или набора элементов, согласно сортировке
array_sum / array_product - сумма и произведение массива

reset prev next end - перемещение указателя
current/pos + key - текущий элемент / ключ в указателе

array_walk - маппинг массива (менее функциональный)
array_walk_recursive - маппинг массива любой вложенности (проходит только по листьям)
array_map - иммутабельный маппинг массива или массива масивов
array_filter / array_reduce - фильтрация и свертка

array_merge — объединяет один или большее количество массивов
array_merge_recursive - хитрое объединение (ничего не перезатирается)
array_replace - Заменяет элементы массива элементами других переданных массивов
array_replace_recursive - рекурсивно заменяет элементы первого массива элементами переданных массивов

array_count_values / array_unique - кол-во уникальных значений / убрать неуникальные
array_column - получить колонку массива по имени
array_reverse - перевернуть массив
array_flip - поменять ключи и значения местами
array_change_key_case — Меняет регистр всех ключей в массиве
array_rand - получить случайный элемент

Пересечения и расхождения (работа с множествами)
array_intersect_key / array_intersect_ukey / array_intersect / array_intersect_assoc / array_intersect_uassoc
array_uintersect / array_uintersect_assoc / array_uintersect_uassoc
array_diff_key / array_diff_ukey / array_diff / array_diff_assoc / array_diff_uassoc
array_udiff / array_udiff_assoc / array_udiff_uassoc
