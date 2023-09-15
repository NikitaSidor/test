<?php

/**
 * Пример запуска:
 *
 *  exSearch::getTours()                                                        - выдаст все туры с шаблоном 41 без учёта дат.
 *  exSearch::getTours([1,2,3,4,...])                                           - выдаст туры с шаблоном 41 и перечисленными id без учёта дат.
 *  exSearch::getTours([], '25.03.2020')                                        - выдаст все туры с шаблоном 41, у которых дата заезда будет после 25 марта 2020 года.
 *  exSearch::getTours([], null, '21.12.2020')                                  - выдаст все туры с шаблоном 41, у которых дата заезда будет до 21 декабря 2020 года.
 *  exSearch::getTours([], '25.03.2020', '21.12.2020')                          - выдаст все туры с шаблоном 41, дата заезда которых находится между двумя датами.
 *  exSearch::getTours([1,2,3,4,...], '25.03.2020', '21.12.2020')               - выдаст все туры с шаблоном 41, id которых равны перечислению, а дата заезда которых находится между двумя датами.
 *  exSearch::getTours([1,2,3,4,...], '2020.03.25', '2020.12.21', 'Y.m.d')      - если даты передаются в нестандартном формате, можно передать маску этих дат и они будут автоматически распознаны.
 *
 *  exSearch::tourFilter([
 *      'tourIds' => [1,2,3,4,...],                                             - массив идентификаторов туров.
 *      'dateFrom' => '25.03.2020',                                             - дата начала.
 *      'dateTo' => '21.12.2020',                                               - дата окончания.
 *      'dateFormat' => 'Y.m.d',                                                - формат даты.
 *      'tourTemplatesIds' => [41, ...],                                        - массив идентификаторов шаблонов для туров.
 *      'dateToMethod' => 'toDuration',                                         - метод фильтрации по продолжительности (toDuration) или по вхождению даты в диапазон (toDate).
 *      'cityFromIds' => [337, ...],                                            - идентификаторы городов отправления.
 *      'priceFrom' => 1000,                                                    - фильтровать туры по стоимости не ниже указанной.
 *      'priceTo' => 3000,                                                      - фильтровать туры по стоимости не выше указанной.
 *      'duration' => 2,                                                        - фильтровать по точной продолжительности в днях.
 *      'durationFrom' => 1,                                                    - фильтровать по продолжительности ОТ в днях.
 *      'durationTo' => 3,                                                      - фильтровать по продолжительности ДО в днях.
 *      'filter' => [162, ...],                                               - фильтровать по типам туров.
 *      'transportType' => [785, ...],                                          - фильтровать по типам транспорта.
 *  ])
 */
class excursionSearch
{
    const DATE_TO_METHOD_TO_DATE = 'toDate';
    const DATE_TO_METHOD_TO_DURATION = 'toDuration';
    const TPL_COUNTRY = 6;
    const TPL_REGION = 24;
    const TPL_RESORT = 25;
    const TPL_CITY = 26;
    const TPL_OBJECT = 27;
    const FILTERS_PARENT_ID = 182;
    /**
     * Метод поиска туров.
     *
     * @param array $tourIds                                                    - массив идентификаторов туров. Если пустой - выбираются все туры.
     * @param string|null $dateFrom                                             - дата начала диапазона. Если null - не учитывается.
     * @param string|null $dateTo                                               - дата начала диапазона. Если null - не учитывается.
     * @param string $dateFormat                                                - строка с форматом передаваемой даты.
     *
     * @return void
     */
    static public function getTours($tourIds = [], $dateFrom = null, $dateTo = null, $dateFormat = 'd.m.Y')
    {
        // Возвращаем результат фильтрации
        return self::tourFilter([
            'tourIds' => $tourIds,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'dateFormat' => $dateFormat,
        ]);
    }

    /**
     * Метод фильтрации туров.
     *
     * @param array $filter                                                     - массив с критериями фильтрации.
     *
     * @return void
     */
    static public function tourFilter(array $filter)
    {
        // Инициируем глобальный инстанс modx
        global $modx;

        // Результаты
        $results = null;

        // Формируем условия основного запроса туров
        $join = [];
        $where = [];

        // Извлекаем переменные из массива фильтра
        $tourIds = self::arrayExtract($filter, 'tourIds', []);
        $dateFrom = self::arrayExtract($filter, 'dateFrom');
        $dateTo = self::arrayExtract($filter, 'dateTo');
        $dateFormat = self::arrayExtract($filter, 'dateFormat', 'd.m.Y');
        $tourTemplatesIds = self::arrayExtract($filter, 'tourTemplatesIds', [41]);
        // toDate, toDuration
        $dateToMethod = self::arrayExtract($filter, 'dateToMethod', self::DATE_TO_METHOD_TO_DATE);
        $cityFromIds = self::arrayExtract($filter, 'cityFromIds', []);
        $priceFrom = self::arrayExtract($filter, 'priceFrom');
        $priceTo = self::arrayExtract($filter, 'priceTo');
        $duration = self::arrayExtract($filter, 'duration');
        $durationFrom = self::arrayExtract($filter, 'durationFrom');
        $durationTo = self::arrayExtract($filter, 'durationTo');
        $filter = self::arrayExtract($filter, 'filter', []);
        $transportType = self::arrayExtract($filter, 'transportType', []);
        $action = self::arrayExtract($filter, 'action');
        $tourDurationType = self::arrayExtract($filter, 'tourDurationType');

        // Дату на этом этапе не проверяем. Потому что на первом этапе мы только резервируем ключи.
        // На втором этапе - выбираем туры и сопоставляем с ключами
        // На последнем этапе удаляем ключи без активных туров
        // И всё это в сумме работает

        // Если передан массив id туров
        if (is_array($tourIds) and !empty($tourIds)) {
            $where = array_merge($where, [
                "AND t.id IN ('" . implode("','", $tourIds) . "')",
            ]);
        }

        // Если передан массив типов туров
        if (is_array($filter)) {
            $filter = array_filter($filter);
        }

        // Если передан массив типов транспорта
        if (is_array($transportType)) {
            $transportType = array_filter($transportType);
        }

        // Если переданы акции
        if (intval($action) > 0) {
            $join = array_merge($join, [
                "LEFT JOIN {$modx->getFullTableName('site_tmplvars')} st_action ON st_action.name = 'action'",
                "LEFT JOIN {$modx->getFullTableName('site_tmplvar_contentvalues')} stc_action ON stc_action.tmplvarid = st_action.id AND stc_action.contentid = t.id",
            ]);

            $where = array_merge($where, [
                "AND stc_action.id IS NOT NULL",
                //"AND stc_action.value = '{$action}'",
                "AND (stc_action.value = '{$action}') OR (stc_action.value LIKE CONCAT('{$action}', '||%')) OR (stc_action.value LIKE CONCAT('%||', '{$action}', '||%')) OR (stc_action.value LIKE CONCAT('%||', '{$action}'))",
            ]);
        }

        // Если передан тип продолжительности туров
        if (!is_null($tourDurationType)) {
            $join = array_merge($join, [
                "LEFT JOIN {$modx->getFullTableName('site_tmplvars')} st_tourDurationType ON st_tourDurationType.name = 'tourDurationType'",
                "LEFT JOIN {$modx->getFullTableName('site_tmplvar_contentvalues')} stc_tourDurationType ON stc_tourDurationType.tmplvarid = st_tourDurationType.id AND stc_tourDurationType.contentid = t.id",
            ]);

            $where = array_merge($where, [
                "AND COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = '{$tourDurationType}'",
            ]);
        }

        // Запрашиваем общее число туров с шаблоном 41
        $toursSummary = self::getList([
            'select' => [
                "t.id",
            ],
            'from' => $modx->getFullTableName('site_content'),
            'join' => array_merge([], $join),
            'where' => array_merge([
                "t.template IN ('" . implode("','", $tourTemplatesIds) . "')",
                "AND t.published = '1'",
            ], $where),
            'order' => [
                "t.id ASC",
            ],
            'group' => [
                "t.id"
            ],
        ]);

        // Если туры есть
        if ($toursSummary) {
            $toursList = [];

            // Формируем массив результатов
            foreach ($toursSummary as $tour) {
                // Резервируем ключи
                $toursList[intval($tour['id'])] = [];
            }

            // Формируем условия для поиска дат и мест
            $select = [];
            $where = [];
            $join = [];
            $order = [];

            $select = array_merge($select, [
                "COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) AS duration_type",
                "DATE_FORMAT(v.date, '%d.%m.%Y') AS date",
                "COALESCE(COALESCE(bs.places_total, v.places), '0') AS total_places",
                "COALESCE(COALESCE(bs.places_total, v.places) - COALESCE(`to`.places_busy, '0'), '0') AS free_places",
            ]);

            $join = array_merge($join, [
                "LEFT JOIN {$modx->getFullTableName('tours_voyages')} tv ON tv.site_content_id = sc.id",
                "LEFT JOIN {$modx->getFullTableName('voyages')} v ON v.id = tv.voyage_out_id",
            ]);

            $order = array_merge($order, [
                "COALESCE(DATE_FORMAT(v.date, '%Y-%m-%d'), 'z') ASC",
                "free_places ASC",
                "duration ASC",
            ]);

            // Если передана дата начала или конца диапазона
            if (!is_null($dateFrom) or !is_null($dateTo)) {
                $where = array_merge($where, [
                    "AND tv.voyage_out_id IS NOT NULL",
                    "AND DATE_FORMAT(v.date, '%Y-%m-%d') >= DATE_FORMAT('" . date('Y-m-d') . "', '%Y-%m-%d')",
                    "AND tv.deleted = '0'",
                ]);

                // Если передана дата начала диапазона
                if (!is_null($dateFrom) and !empty($dateFrom)) {
                    // Конвертируем дату в формат БД
                    $formatDate = self::dateConvert($dateFrom, $dateFormat, 'Y-m-d');

                    // Ограничиваем результаты турами старше даты начала диапазона
                    $where = array_merge($where, [
                        "AND DATE_FORMAT(v.date, '%Y-%m-%d') >= DATE_FORMAT('" . $formatDate . "', '%Y-%m-%d')",
                    ]);
                }

                // Если передана дата конца диапазона
                if (!is_null($dateTo) and !empty($dateTo)) {
                    // Конвертируем дату в формат БД
                    $formatDate = self::dateConvert($dateTo, $dateFormat, 'Y-m-d');

                    // Выбираем метод ограничения дат поиска
                    switch ($dateToMethod) {
                        case self::DATE_TO_METHOD_TO_DURATION:
                            // Ограничиваем результаты продолжительностью тура до переданной даты окончания
                            $where = array_merge($where, [
                                "AND DATE_FORMAT(FROM_UNIXTIME(UNIX_TIMESTAMP(STR_TO_DATE(DATE_FORMAT(v.date, '%Y-%m-%d'), '%Y-%m-%d')) + IF(COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = 'day', (IF(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) > 1, COALESCE(stc_tourDuration.value, st_tourDuration.default_text), 0) * 24 * 60 * 60), IF(COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = 'hour', IF(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) > 24, COALESCE(stc_tourDuration.value, st_tourDuration.default_text), 24) * 60 * 60, 0))), '%Y-%m-%d') <= DATE_FORMAT('" . $formatDate . "', '%Y-%m-%d')",
                            ]);

                            break;
                        case self::DATE_TO_METHOD_TO_DATE:
                        default:
                            // Ограничиваем результаты турами младше даты конца диапазона
                            $where = array_merge($where, [
                                "AND DATE_FORMAT(v.date, '%Y-%m-%d') <= DATE_FORMAT('" . $formatDate . "', '%Y-%m-%d')",
                            ]);

                            break;
                    }
                }
            }

            // Формируем подзапрос подсчёта общего числа мест в автобусе
            $ebsSql = self::getRawSql([
                'select' => [
                    "bs.site_content_id",
                    "COUNT(bp.bus_scheme_id) AS places_total",
                ],
                'from' => $modx->getFullTableName('bus_schemes'),
                'alias' => "bs",
                'join' => [
                    "LEFT JOIN {$modx->getFullTableName('bus_places')} bp ON bp.bus_scheme_id = bs.id",
                ],
                'where' => [
                    "bp.type = 'passenger'",
                ],
                'group' => [
                    "bs.site_content_id",
                ],
            ]);

            // Формируем подзапрос подсчёта занятых мест в автобусе на дату
            $etoSql = self::getRawSql([
                'select' => [
                    "`to`.voyage_id",
                    "COUNT(top.place) AS places_busy",
                ],
                'from' => $modx->getFullTableName('tours_orders'),
                'alias' => "`to`",
                'join' => [
                    "LEFT JOIN {$modx->getFullTableName('tours_orders_places')} top ON top.order_id = `to`.id",
                ],
                'group' => [
                    "`to`.voyage_id",
                ],
            ]);

            $join = array_merge($join, [
                "LEFT JOIN (" . $ebsSql . ") bs ON bs.site_content_id = v.bus_scheme_id",
                "LEFT JOIN (" . $etoSql . ") `to` ON `to`.voyage_id = tv.id",
            ]);

            $select = array_merge($select, [
                "COALESCE(`to`.places_busy, '0') AS busy_places",
            ]);

            // Проверим идентификаторы городов отправления на адекватность
            if (is_array($cityFromIds) and !empty($cityFromIds)) {
                foreach ($cityFromIds as $key => $value) {
                    if (intval($value) <= 0) {
                        unset($cityFromIds[$key]);
                    }
                }
            }

            // Если переданы идентификаторы городов отправления
            if (is_array($cityFromIds) and !empty($cityFromIds)) {
                // Ограничиваем результаты городами отправления
                $where = array_merge($where, [
                    "AND v.curort_id IN ('" . implode("','", $cityFromIds) . "')",
                ]);
            }

            // Если переданы диапазоны цен
            if (!is_null($priceFrom) or !is_null($priceTo)) {
                $join = array_merge($join, [
                    "LEFT JOIN {$modx->getFullTableName('site_tmplvars')} st_price ON st_price.name = 'price'",
                    "LEFT JOIN {$modx->getFullTableName('site_tmplvar_contentvalues')} stc_price ON stc_price.tmplvarid = st_price.id AND stc_price.contentid = sc.id",
                ]);

                // Если передана стоимость ОТ
                if (!is_null($priceFrom)) {
                    // Ограничиваем результаты начальной стоимостью
                    $where = array_merge($where, [
                        "AND IF(COALESCE(stc_price.value, st_price.default_text) > 0, COALESCE(stc_price.value, st_price.default_text), 0) >= " . intval($priceFrom) . "",
                    ]);
                }

                // Проверка цены ДО на адекватность
                if (!is_null($priceTo) and !is_null($priceFrom) and (($priceTo < $priceFrom) or ($priceTo <= 0))) {
                    $priceTo = null;
                }

                // Если передана стоимость ДО
                if (!is_null($priceTo)) {
                    // Ограничиваем результаты начальной стоимостью
                    $where = array_merge($where, [
                        "AND IF(COALESCE(stc_price.value, st_price.default_text) > 0, COALESCE(stc_price.value, st_price.default_text), 0) <= " . intval($priceTo) . "",
                    ]);
                }
            }

            // Если передана точная продолжительность
            if (!is_null($duration)) {
                // Если передан массив продолжительностей
                if (is_array($duration) and !empty($duration)) {
                    // Ограничиваем результаты диапазоном продолжительностей
                    $where = array_merge($where, [
                        "AND IF(COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = 'day', IF(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) > 1, COALESCE(stc_tourDuration.value, st_tourDuration.default_text), 0), IF(COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = 'hour', IF(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) > 24, CEIL(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) / 24), 1), 0)) IN ('" . implode("','", $duration) . "')",
                    ]);
                } else {
                    // Иначе передана продолжительность

                    // Ограничиваем результаты точной продолжительностью
                    $where = array_merge($where, [
                        "AND IF(COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = 'day', IF(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) > 1, COALESCE(stc_tourDuration.value, st_tourDuration.default_text), 0), IF(COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = 'hour', IF(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) > 24, CEIL(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) / 24), 1), 0)) = '" . intval($duration) . "'",
                    ]);
                }
            } else {
                // Если передали диапазон продолжительностей ОТ
                if (!is_null($durationFrom)) {
                    // Ограничиваем результаты продолжительностью ОТ
                    $where = array_merge($where, [
                        "AND IF(COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = 'day', IF(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) > 1, COALESCE(stc_tourDuration.value, st_tourDuration.default_text), 0), IF(COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = 'hour', IF(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) > 24, CEIL(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) / 24), 1), 0)) >= '" . intval($durationFrom) . "'",
                    ]);
                }

                if (!is_null($durationTo)) {
                    // Ограничиваем результаты продолжительностью ДО
                    $where = array_merge($where, [
                        "AND IF(COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = 'day', IF(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) > 1, COALESCE(stc_tourDuration.value, st_tourDuration.default_text), 0), IF(COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = 'hour', IF(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) > 24, CEIL(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) / 24), 1), 0)) <= '" . intval($durationTo) . "'",
                    ]);
                }
            }

            $join = array_merge($join, [
                "LEFT JOIN {$modx->getFullTableName('site_tmplvars')} st_tourDuration ON st_tourDuration.name = 'tourDuration'",
                "LEFT JOIN {$modx->getFullTableName('site_tmplvar_contentvalues')} stc_tourDuration ON stc_tourDuration.tmplvarid = st_tourDuration.id AND stc_tourDuration.contentid = sc.id",
                "LEFT JOIN {$modx->getFullTableName('site_tmplvars')} st_tourDurationType ON st_tourDurationType.name = 'tourDurationType'",
                "LEFT JOIN {$modx->getFullTableName('site_tmplvar_contentvalues')} stc_tourDurationType ON stc_tourDurationType.tmplvarid = st_tourDurationType.id AND stc_tourDurationType.contentid = sc.id",
            ]);

            // Если передан тип продолжительности туров
            if (!is_null($tourDurationType)) {
                $where = array_merge($where, [
                    "AND COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = '{$tourDurationType}'",
                ]);
            }

            // Если переданы типы туров
            if (is_array($filter) and !empty($filter)) {
                // Формируем условия точного совпадения
                $join = array_merge($join, [
                    "LEFT JOIN {$modx->getFullTableName('tour_types_to_content')} tttc_tour_type ON tttc_tour_type.contentid = sc.id",
                ]);

                $where = array_merge($where, [
                    "AND tttc_tour_type.filterid IN ('" . implode("', '", $filter) . "')",
                ]);
            }

            // Если переданы типы транспорта
            if (is_array($transportType) and !empty($transportType)) {
                // Формируем условия точного совпадения
                $join = array_merge($join, [
                    "LEFT JOIN {$modx->getFullTableName('tour_types_to_content')} tttc_transport_type ON tttc_transport_type.contentid = sc.id",
                ]);

                $where = array_merge($where, [
                    "AND tttc_transport_type.filterid IN ('" . implode("', '", $transportType) . "')",
                ]);
            }

            // Если переданы акции
            if (intval($action) > 0) {
                $join = array_merge($join, [
                    "LEFT JOIN {$modx->getFullTableName('site_tmplvars')} st_action ON st_action.name = 'action'",
                    "LEFT JOIN {$modx->getFullTableName('site_tmplvar_contentvalues')} stc_action ON stc_action.tmplvarid = st_action.id AND stc_action.contentid = sc.id",
                ]);

                $where = array_merge($where, [
                    "AND stc_action.id IS NOT NULL",
                    //"AND stc_action.value = '{$action}'",
                    "AND (stc_action.value = '{$action}') OR (stc_action.value LIKE CONCAT('{$action}', '||%')) OR (stc_action.value LIKE CONCAT('%||', '{$action}', '||%')) OR (stc_action.value LIKE CONCAT('%||', '{$action}'))",
                ]);
            }

            // Запрашиваем даты туров с местами
            $tourDates = self::getList([
                'select' => array_merge([
                    "sc.id",
                    "IF(COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = 'day', IF(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) > 1, COALESCE(stc_tourDuration.value, st_tourDuration.default_text), 0), IF(COALESCE(stc_tourDurationType.value, st_tourDurationType.default_text) = 'hour', IF(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) > 24, CEIL(COALESCE(stc_tourDuration.value, st_tourDuration.default_text) / 24), 1), 0)) AS duration",
                ], $select),
                'from' => $modx->getFullTableName('site_content'),
                'alias' => "sc",
                'join' => array_merge([], $join),
                'where' => array_merge([
                    "sc.template IN ('" . implode("','", $tourTemplatesIds) . "')",
                    "AND sc.published = '1'",
                    "AND sc.id IN ('" . implode("','", array_keys($toursList)) . "')",
                ], $where),
                'group' => [
                    "sc.id",
                ],
                'order' => array_merge([], $order),
            ]);

            // Если результат есть
            if ($tourDates) {
                // Проходимся по массиву
                foreach ($tourDates as $tourDate) {
                    if (!array_key_exists(intval($tourDate['id']), $toursList)) {
                        continue;
                    }

                    if (!is_array($results)) {
                        $results = [];
                    }

                    // Формализуем данные по найденным турам
                    $results[intval($tourDate['id'])][] = $tourDate;
                }
            }

            // Если передана дата начала или конца диапазона
            //if (!is_null($dateFrom) or !is_null($dateTo)) {
            // Далее просто удаляем все пустые туры
            if (is_array($results) and !empty($results)) {
                foreach ($results as $index => $result) {
                    if (empty($result)) {
                        // Они нам не нужны, так как мы запрашиваем данные с датами
                        unset($results[$index]);
                    }
                }
            }
            //}
        }

        // Возвращаем результат
        return $results;
    }

    static public function getHotels(array $filter)
    {
        // Инициируем глобальный инстанс modx
        global $modx;

        // Результаты
        $results = null;

        // Формируем условия основного запроса туров
        $join = [];
        $where = [];

        // Извлекаем переменные из массива фильтра
        $countryIds = self::arrayExtract($filter, 'countryIds', []);
        $cityIds = self::arrayExtract($filter, 'cityIds', []);
        $filterIds = self::arrayExtract($filter, 'filterIds', []);
        $stars = self::arrayExtract($filter, 'stars', []);
        $name = self::arrayExtract($filter, 'name');

        // Если в фильтр передана страна
        // Условие учитывает нарушение топологии в пределах 4х уровней
        if (is_array($countryIds) and !empty($countryIds)) {
            $where = array_merge($where, [
                "AND ((psc.template = '" . self::TPL_COUNTRY . "' AND psc.id IN ('" . implode("', '", $countryIds) . "')) OR (ppsc.template = '" . self::TPL_COUNTRY . "' AND ppsc.id IN ('" . implode("', '", $countryIds) . "')) OR (pppsc.template = '" . self::TPL_COUNTRY . "' AND pppsc.id IN ('" . implode("', '", $countryIds) . "')) OR (ppppsc.template = '" . self::TPL_COUNTRY . "' AND ppppsc.id IN ('" . implode("', '", $countryIds) . "')))",
            ]);
        }

        // Если в фильтр передан город
        // Условие учитывает нарушение топологии в пределах 4х уровней
        if (is_array($cityIds) and !empty($cityIds)) {
            $where = array_merge($where, [
                "AND ((psc.template = '" . self::TPL_CITY . "' AND psc.id IN ('" . implode("', '", $cityIds) . "')) OR (ppsc.template = '" . self::TPL_CITY . "' AND ppsc.id IN ('" . implode("', '", $cityIds) . "')) OR (pppsc.template = '" . self::TPL_CITY . "' AND pppsc.id IN ('" . implode("', '", $cityIds) . "')) OR (ppppsc.template = '" . self::TPL_CITY . "' AND ppppsc.id IN ('" . implode("', '", $cityIds) . "')))",
            ]);
        }

        if (is_array($filterIds) and !empty($filterIds)) {
            $join = array_merge($join, [
                // Выборка типов (если передан фильтр типов)
                "LEFT JOIN {$modx->getFullTableName('site_tmplvars')} st_types ON st_types.name = 'filter'",
                "LEFT JOIN {$modx->getFullTableName('site_tmplvar_contentvalues')} stc_types ON stc_types.tmplvarid = st_types.id AND stc_types.contentid = sc.id",
                "LEFT JOIN (" . self::getRawSql([
                    'alias' => "sc",
                    'select' => [
                        "sc.id",
                    ],
                    'from' => $modx->getFullTableName('site_content'),
                    'join' => [
                        "LEFT JOIN {$modx->getFullTableName('site_content')} psc ON psc.id = sc.parent",
                        "LEFT JOIN {$modx->getFullTableName('site_content')} ppsc ON ppsc.id = psc.parent",
                    ],
                    'where' => [
                        "sc.published = '1'",
                        "AND psc.published = '1'",
                        "AND ppsc.id = '" . self::FILTERS_PARENT_ID . "'",
                        "AND sc.id IN ('" . implode("', '", $filterIds) . "')",
                    ],
                    'order' => [
                        "sc.id",
                    ],
                    'group' => [
                        "sc.id",
                    ],
                ]) . ") types ON (stc_types.value = types.id) OR (stc_types.value LIKE CONCAT(types.id, '||%')) OR (stc_types.value LIKE CONCAT('%||', types.id, '||%')) OR (stc_types.value LIKE CONCAT('%||', types.id))",
            ]);

            $where = array_merge($where, [
                "AND stc_types.id IS NOT NULL",
                "AND types.id IS NOT NULL",
            ]);
        }

        if (is_array($stars) and !empty($stars)) {
            $join = array_merge($join, [
                "LEFT JOIN {$modx->getFullTableName('site_tmplvars')} st_stars ON st_stars.name = 'hotelStars'",
                "LEFT JOIN {$modx->getFullTableName('site_tmplvar_contentvalues')} stc_stars ON stc_stars.tmplvarid = st_stars.id AND stc_stars.contentid = sc.id",
            ]);

            $where = array_merge($where, [
                "AND stc_stars.value IN ('" . implode("', '", $stars) . "')",
            ]);
        }

        if (is_string($name) and !empty($name)) {
            $where = array_merge($where, [
                "AND sc.pagetitle LIKE('%{$name}%')",
            ]);
        }

        $results = self::getList([
            'alias' => "sc",
            'select' => [
                "sc.id",
            ],
            'from' => $modx->getFullTableName('site_content'),
            'join' => array_merge([
                // Родитель отеля (город)
                "LEFT JOIN {$modx->getFullTableName('site_content')} psc ON psc.id = sc.parent",
                // Родитель города (курорт)
                "LEFT JOIN {$modx->getFullTableName('site_content')} ppsc ON ppsc.id = psc.parent",
                // Родитель курорта (регион)
                "LEFT JOIN {$modx->getFullTableName('site_content')} pppsc ON pppsc.id = ppsc.parent",
                // Родитель региона (страна)
                "LEFT JOIN {$modx->getFullTableName('site_content')} ppppsc ON ppppsc.id = pppsc.parent",
            ], $join),
            'where' => array_merge([
                "sc.published = '1'",
                "AND sc.template = " . self::TPL_OBJECT
            ], $where),
            'group' => [
                "sc.id",
            ],
            'order' => [
                "sc.pagetitle",
            ],
        ]);

        if (is_array($results) and !empty($results)) {
            return array_map(function ($hotel) {
                return intval($hotel['id']);
            }, $results);
        }

        return null;
    }

    /**
     * Метод запроса списка данных.
     *
     * @param array $filter                                                     - массив данных для постройки запроса.
     *
     * @return void
     */
    static protected function getList($filter = [])
    {
        // Инициируем глобальный инстанс modx
        global $modx;

        // Получаем ресурс запроса
        $resource = self::search($filter);

        // Проверяем, что результат есть
        if ($modx->db->getRecordCount($resource)) {
            $results = [];

            // Получаем результрат построчно
            while ($row = $modx->db->getRow($resource)) {
                // И запоминаем результат
                $results[] = $row;
            }

            // Возвращаем результат
            return $results;
        }

        // Иначе возвращаем null
        return null;
    }

    /**
     * Метод получения поискового ресурса.
     *
     * @param array $filter                                                     - массив данных для постройки запроса.
     *
     * @return void
     */
    static protected function search($filter = [])
    {
        // Инициируем глобальный инстанс modx
        global $modx;

        // Получаем "чистый" SQL-запрос на основании параметров, указанных в фильтре
        $query = self::getRawSql($filter);


        // echo '<pre>';
        // var_dump($query);
        // echo '</pre>';
        // exit;

        // Возвращаем ресурс с результатом запроса
        return $modx->db->query($query);
    }

    /**
     * Метод формирования SQL.
     *
     * @param array $filter                                                     - массив данных для постройки запроса.
     *
     * @return void
     */
    static protected function getRawSql($filter = [])
    {
        // Конструируем запрос на основании полей фильтра
        if (!array_key_exists('alias', $filter)) {
            $filter['alias'] = 't';
        }

        if (!array_key_exists('select', $filter)) {
            $filter['select'] = $filter['alias'] . '.*';
        }

        if (!is_array($filter['select'])) {
            $filter['select'] = [$filter['select']];
        }

        return "SELECT" . PHP_EOL
            . "\t" . implode("," . PHP_EOL . "\t", $filter['select']) . PHP_EOL
            . "FROM " . $filter['from'] . " " . $filter['alias'] . PHP_EOL
            . (!empty($filter['join']) ? implode(PHP_EOL, $filter['join']) . PHP_EOL : "")
            . (!empty($filter['where']) ? "WHERE" . PHP_EOL . "\t" . implode(PHP_EOL . "\t", $filter['where']) . PHP_EOL : "")
            . (!empty($filter['group']) ? "GROUP BY" . PHP_EOL . "\t" . implode("," . PHP_EOL . "\t", $filter['group']) . PHP_EOL : "")
            . (!empty($filter['having']) ? "HAVING" . PHP_EOL . "\t" . implode(PHP_EOL . "\t", $filter['having']) . PHP_EOL : "")
            . (!empty($filter['order']) ? "ORDER BY" . PHP_EOL . "\t" . implode("," . PHP_EOL . "\t", $filter['order']) . PHP_EOL : "")
            . (!empty($filter['limit']) ? "LIMIT " . intval($filter['limit']) . PHP_EOL : "")
            . (!empty($filter['offset']) ? "OFFSET " . intval($filter['offset']) . PHP_EOL : "");
    }

    /**
     * Метод конвертации даты.
     *
     * @param string $date                                                      - строка, содержащая дату.
     * @param string $formatFrom                                                - формат входящей даты.
     * @param string $formatTo                                                  - формат исходящей даты.
     *
     * @return void
     */
    static protected function dateConvert($date, $formatFrom = 'Y-m-d H:i:s', $formatTo = 'H:i d.m.Y')
    {
        // Если дата - это не пустая строка
        if (is_string($date) and !empty($date)) {
            // Создаём объект типа DateTime
            $dateTime = DateTime::createFromFormat($formatFrom, $date);

            // Если входные данные были верны и мы получили объект типа DateTime
            if ($dateTime instanceof DateTime) {
                // Возвращаем форматированную дату
                return $dateTime->format($formatTo);
            }
        }

        // Иначе возвращаем null
        return null;
    }

    /**
     * Метод извлечения значений из массива фильтра.
     *
     * @param array $filter                                                     - массив с полями фильтра.
     * @param mixed $key                                                        - ключ массива.
     * @param mixed|null $defaultValue                                          - значение по-умолчанию, если ключ не найден.
     *
     * @return void
     */
    static protected function arrayExtract(array $filter, $key, $defaultValue = null)
    {
        // Если фильтр - это не пустой массив и в нём есть переданный ключ
        if (is_array($filter) and !empty($filter) and array_key_exists($key, $filter)) {
            // Возвращаем значение, соответствующее ключу
            return $filter[$key];
        }

        // Иначе возвращаем значение по-умолчанию
        return $defaultValue;
    }
}
