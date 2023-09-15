<?php

// use \kreexus\lk\app\models\SiteContentCitiesModel;
// use \kreexus\lk\app\models\SiteContentCountriesModel;
// use \kreexus\lk\app\models\SiteContentObjectsModel;
// use \kreexus\lk\app\models\SiteContentRegionsModel;
// use \kreexus\lk\app\models\SiteContentResortsModel;

require_once realpath($_SERVER['DOCUMENT_ROOT'] . '/assets/helpers/searchHelper.php');

class HotelHelper extends SearchHelper
{
    public static $geo;
    public static $geoType;
    public static $geoTypes = ['city', 'resort', 'region', 'country']; // order is important
    const TPL_COUNTRY = 3;
    const TPL_REGION = 48;
    const TPL_RESORT = 49;
    const TPL_CITY = 50;
    const TPL_OBJECT = 10;

    // protected static function availableHotelSql($name)
    // {
    //     global $modx;

    //     return "SELECT
    //             `stc`.`value`
    //         FROM " . $modx->getFullTableName('site_content') . " `sc`
    //         LEFT JOIN " . $modx->getFullTableName('site_tmplvars') . " `st` ON `st`.`name` = '{$name}'
    //         LEFT JOIN " . $modx->getFullTableName('site_tmplvar_contentvalues') . " `stc` ON `stc`.`tmplvarid` = `st`.`id` AND `stc`.`contentid` = `sc`.`id`
    //         WHERE
    //             `sc`.`published` = '1'
    //             AND `sc`.`template` = '" . self::TPL_OBJECT . "'
    //             AND `stc`.`id` IS NOT NULL
    //         GROUP BY
    //             `stc`.`value`
    //         ORDER BY
    //             `stc`.`id`";
    // }

    public static function getHotelTypes()
    {
        global $modx;

        // $sql = "SELECT
        //         `sc`.`id`
        //     FROM " . $modx->getFullTableName('site_content') . " `sc`
        //     LEFT JOIN " . $modx->getFullTableName('site_content') . " `psc` ON `psc`.`id` = `sc`.`parent`
        //     LEFT JOIN " . $modx->getFullTableName('site_content') . " `ppsc` ON `ppsc`.`id` = `psc`.`parent`
        //     LEFT JOIN (" . self::availableHotelSql('filter') . ") `avail` ON (`avail`.`value` = `sc`.`id`) OR (`avail`.`value` LIKE CONCAT(`sc`.`id`, '||%')) OR (`avail`.`value` LIKE CONCAT('%||', `sc`.`id`, '||%')) OR (`avail`.`value` LIKE CONCAT('%||', `sc`.`id`))
        //     WHERE
        //         `sc`.`published` = '1'
        //         AND `psc`.`published` = '1'
        //         AND `ppsc`.`id` = '" . self::ATTRIBUTES_ROOT . "'
        //         AND `avail`.`value` IS NOT NULL
        //     GROUP BY
        //         `sc`.`id`
        //     ORDER BY
        //         `sc`.`id`";

        $sql = "SELECT ftc.filterid as id FROM {$modx->getFullTableName('filters_to_content')} ftc
        LEFT JOIN {$modx->getFullTableName('site_content')} sc ON sc.id = ftc.contentid
        WHERE sc.template = " . self::TPL_OBJECT . "
        GROUP BY ftc.filterid
        ORDER BY ftc.filterid";

        $relevantTypes = array_keys(self::selectBySql($sql));

        $categories = $modx->getDocumentChildren(self::ATTRIBUTES_ROOT, 1, 0, 'id, pagetitle', '', 'menuindex, pagetitle', 'ASC');

        $categoriesData = [];

        if (is_array($categories) and !empty($categories)) {
            $categories = array_filter($categories, function ($category) use (&$categoriesData, &$modx, $relevantTypes) {
                $childrens = $modx->getDocumentChildren($category['id'], 1, 0, 'id, pagetitle', '', 'menuindex, pagetitle', 'ASC');

                if (is_array($childrens) and !empty($childrens)) {
                    $childrens = array_filter($childrens, function ($children) use ($relevantTypes) {
                        if (in_array(intval($children['id']), $relevantTypes)) {
                            return true;
                        }

                        return false;
                    });

                    $childrens = array_map(function ($children) {
                        $children['id'] = intval($children['id']);

                        return $children;
                    }, $childrens);
                }

                if (!empty($childrens)) {
                    $categoriesData[$category['id']] = [
                        'id' => intval($category['id']),
                        'pagetitle' => $category['pagetitle'],
                        'children' => $childrens,
                    ];
                }

                return true;
            });
        }

        return $categoriesData;
    }

    public static function getCountries($tv = [])
    {
        // return self::matchGeo(self::TPL_OBJECT, self::TPL_COUNTRY, $tv);
        return self::getGeoDataForSelectByTemplate(self::TPL_COUNTRY);
    }

    public static function getRegions($tv = [])
    {
        // return self::matchGeo(self::TPL_OBJECT, self::TPL_REGION, $tv);
        return self::getGeoDataForSelectByTemplate(self::TPL_REGION);
    }

    public static function getResorts($tv = [])
    {
        // return self::matchGeo(self::TPL_OBJECT, self::TPL_RESORT, $tv);
        return self::getGeoDataForSelectByTemplate(self::TPL_RESORT);
    }

    public static function getCities($tv = [])
    {
        // return self::matchGeo(self::TPL_OBJECT, self::TPL_CITY, $tv);
        return self::getGeoDataForSelectByTemplate(self::TPL_CITY);
    }

    public static function getStars($tv = [])
    {
        global $modx;

        $sql = "SELECT
                `stc`.`value` AS `id`,
                `stc`.`value` AS `value`
            FROM " . $modx->getFullTableName('site_content') . " `sc`
            LEFT JOIN " . $modx->getFullTableName('site_tmplvars') . " `st` ON `st`.`name` = 'hotelStars'
            LEFT JOIN " . $modx->getFullTableName('site_tmplvar_contentvalues') . " `stc` ON `stc`.`tmplvarid` = `st`.`id` AND `stc`.`contentid` = `sc`.`id`
            WHERE
                `sc`.`published` = '1'
                AND `sc`.`template` = '" . self::TPL_OBJECT . "'
                AND `stc`.`id` IS NOT NULL
            GROUP BY
                `stc`.`value`
            HAVING
                `value` > '0'
            ORDER BY
                `stc`.`value`";

        return self::selectBySql($sql);
    }

    public static function getHotelTypesDecorated($any = 'Не выбрано')
    {
        return array_map(function ($value) use ($any) {
            if (array_key_exists('children', $value) and is_array($value['children']) and !empty($value['children'])) {
                $value['children'] = [$any] + array_combine(array_values(array_map(function ($value) {
                    return $value['id'];
                }, $value['children'])), array_values(array_map(function ($value) {
                    return $value['pagetitle'];
                }, $value['children'])));
            }

            return $value;
        }, self::getHotelTypes());
    }

    public static function getCountriesDecorated($tv = [], $any = 'Любая')
    {
        return [$any] + self::getCountries($tv);
    }

    public static function getRegionsDecorated($tv = [], $any = 'Любой')
    {
        return [$any] + self::getRegions($tv);
    }

    public static function getResortsDecorated($tv = [], $any = 'Любой')
    {
        return [$any] + self::getResorts($tv);
    }

    public static function getCitiesDecorated($tv = [], $any = 'Любой')
    {
        return [$any] + self::getCities($tv);
    }

    public static function getStarsDecorated($tv = [], $any = 'Любая')
    {
        return [$any] + self::getStars($tv);
    }

    protected static function matchGeoNode($level, $currentTemplateId, $matchedTemplateId, $tv = [])
    {
        global $modx;

        $where = "";

        switch ($level) {
            case 3:
                $join = "        LEFT JOIN {$modx->getfulltablename('site_content')} sct ON sct.id = sc.parent
        LEFT JOIN {$modx->getfulltablename('site_content')} sctt ON sctt.id = sct.parent
        LEFT JOIN {$modx->getfulltablename('site_content')} scttt ON scttt.id = sctt.parent";
                $parent = 'scttt';

                break;
            case 2:
                $join = "        LEFT JOIN {$modx->getfulltablename('site_content')} sct ON sct.id = sc.parent
        LEFT JOIN {$modx->getfulltablename('site_content')} sctt ON sctt.id = sct.parent";
                $parent = 'sctt';

                break;
            case 1:
                $join = "        LEFT JOIN {$modx->getfulltablename('site_content')} sct ON sct.id = sc.parent";
                $parent = 'sct';

                break;
            case 0:
            default:
                $join = "";

                $parent = 'sc';

                break;
        }

        if (is_array($tv) and !empty($tv)) {
            foreach ($tv as $name => $value) {
                $join .= "
        LEFT JOIN evo_site_tmplvars st_{$name} ON st_{$name}.name = '{$name}'
        LEFT JOIN evo_site_tmplvar_contentvalues stc_{$name} ON stc_{$name}.tmplvarid = st_{$name}.id AND stc_{$name}.contentid = sc.id";

                $where .= "
        AND ((COALESCE(stc_{$name}.value, st_{$name}.default_text) = '{$value}') OR (COALESCE(stc_{$name}.value, st_{$name}.default_text) LIKE '{$value}||%') OR (COALESCE(stc_{$name}.value, st_{$name}.default_text) LIKE '%||{$value}||%') OR (COALESCE(stc_{$name}.value, st_{$name}.default_text) LIKE '%||{$value}'))";
            }
        }

        return "SELECT
            scp.id,
            scp.pagetitle as pagetitle,
            scp.parent
        FROM {$modx->getfulltablename('site_content')} sc
        {$join}
        LEFT JOIN {$modx->getfulltablename('site_content')} scp ON scp.id = {$parent}.parent
        WHERE
            sc.template = '{$currentTemplateId}'
            AND sc.published = '1'
            AND sc.deleted = '0'
            AND scp.template = '{$matchedTemplateId}'
            AND scp.published = '1'
            AND scp.deleted = '0'
            AND scp.hidemenu = '0'
        {$where}
        GROUP BY
            scp.id";
    }

    protected static function matchGeo($currentTemplateId, $matchedTemplateId, $tv = [])
    {
        global $modx;

        $results = [];

        $sql = "";

        $queries = [];

        for ($i = 0; $i < 4; $i++) {
            $queries[] = self::matchGeoNode($i, $currentTemplateId, $matchedTemplateId, $tv);
        }

        if (is_array($queries) and !empty($queries)) {
            $sql = implode(" UNION ", $queries) . ' ORDER BY pagetitle';
        }

        $resource = $modx->db->query($sql);

        while ($row = $modx->db->getRow($resource)) {
            $results[$row['id']] = [
                'id' => $row['id'],
                'parent' => $row['parent'],
                'value' => $row['pagetitle'],
            ];
        }

        return $results;
    }

    protected static function getGeo()
    {
        global $modx;

        if (!isset($modx->data['geography'])) {
            $sql = "SELECT
            scttt.id as level_4_id,
            scttt.template as level_4_template,
            scttt.pagetitle as level_4_pagetitle,
            scttt.published as level_4_published,
            scttt.deleted as level_4_deleted,
            scttt.hidemenu as level_4_hidemenu,
            sctt.id as level_3_id,
            sctt.template as level_3_template,
            sctt.pagetitle as level_3_pagetitle,
            sctt.published as level_3_published,
            sctt.deleted as level_3_deleted,
            sctt.hidemenu as level_3_hidemenu,
            sct.id as level_2_id,
            sct.template as level_2_template,
            sct.pagetitle as level_2_pagetitle,
            sct.published as level_2_published,
            sct.deleted as level_2_deleted,
            sct.hidemenu as level_2_hidemenu,
            sc.id as level_1_id,
            sc.template as level_1_template,
            sc.pagetitle as level_1_pagetitle

            FROM {$modx->getfulltablename('site_content')} sc
            LEFT JOIN {$modx->getfulltablename('site_content')} sct ON sct.id = sc.parent
            AND sct.published = '1' AND sct.deleted = '0' AND sct.hidemenu  = '0'

            LEFT JOIN {$modx->getfulltablename('site_content')} sctt ON sctt.id = sct.parent
            AND sctt.published = '1' AND sctt.deleted = '0' AND sctt.hidemenu  = '0'

            LEFT JOIN {$modx->getfulltablename('site_content')} scttt ON scttt.id = sctt.parent
            AND scttt.published = '1' AND scttt.deleted = '0' AND scttt.hidemenu  = '0'

            WHERE
            sc.template = " . self::TPL_CITY . "
            AND sc.published = '1'
            AND sc.deleted = '0'
            AND sc.hidemenu  = '0'

            GROUP BY
            sc.id

            ORDER BY
            scttt.pagetitle,
            sctt.pagetitle,
            sct.pagetitle,
            sc.pagetitle";

            $resource = $modx->db->query($sql);
            while ($row = $modx->db->getRow($resource)) {
                $array = [];
                for ($i = 2; $i <= 4; $i++) {
                    if (
                        !is_null($row["level_{$i}_id"])
                        && ($row["level_{$i}_deleted"] != 0 || $row["level_{$i}_hidemenu"] != 0 || $row["level_{$i}_published"] != 1)
                    ) {
                        echo '<br>' . $i . " BREAK; " . $row['level_1_pagetitle'] . '<br>';
                        $array = [];
                        break;
                    }
                    if ($row["level_{$i}_template"] == self::TPL_RESORT) {
                        $array["resort_id"] = $row["level_{$i}_id"];
                        $array["resort_template"] = $row["level_{$i}_template"];
                        $array["resort_pagetitle"] = $row["level_{$i}_pagetitle"];
                    } else if ($row["level_{$i}_template"] == self::TPL_REGION) {
                        $array["region_id"] = $row["level_{$i}_id"];
                        $array["region_template"] = $row["level_{$i}_template"];
                        $array["region_pagetitle"] = $row["level_{$i}_pagetitle"];
                    } else if ($row["level_{$i}_template"] == self::TPL_COUNTRY) {
                        $array["country_id"] = $row["level_{$i}_id"];
                        $array["country_template"] = $row["level_{$i}_template"];
                        $array["country_pagetitle"] = $row["level_{$i}_pagetitle"];
                    }
                }
                if ($array) {
                    $array['city_id'] = $row['level_1_id'];
                    $array['city_template'] = $row['level_1_template'];
                    $array['city_pagetitle'] = $row['level_1_pagetitle'];
                    $modx->data['geography'][] = $array;
                }
            }
        }

        self::$geo = $modx->data['geography'];
        return self::$geo;
    }

    public static function getGeoTypeByTemplate($matchedTemplateId)
    {
        switch ($matchedTemplateId) {
            case self::TPL_CITY:
                self::$geoType = 'city';
                break;
            case self::TPL_RESORT:
                self::$geoType = 'resort';
                break;
            case self::TPL_REGION:
                self::$geoType = 'region';
                break;
            case self::TPL_COUNTRY:
                self::$geoType = 'country';
                break;
            default:
                self::$geoType = false;
                break;
        }
        return self::$geoType;
    }

    public static function sortGeoPagetitlesByTemplate($matchedTemplateId)
    {
        if (is_null(self::$geo))
            self::$geo = self::getGeo();
        if (is_null(self::$geoType))
            self::$geoType = self::getGeoTypeByTemplate($matchedTemplateId);

        $target = array();
        if (!empty(self::$geo)) {
            foreach (self::$geo as $key => $row) {
                $target[$key] = $row[self::$geoType . '_pagetitle'];
            }
            array_multisort($target, SORT_ASC, self::$geo);
        }
        return self::$geo;
    }

    public static function getGeoDataForSelectByTemplate($matchedTemplateId)
    {
        self::sortGeoPagetitlesByTemplate($matchedTemplateId);
        if (empty(self::$geo)) return [];
        $parentFields = null;
        foreach (self::$geoTypes as $type) {
            if ($type == self::$geoType) {
                $parentFields = [];
            } else if (is_array($parentFields)) {
                $parentFields[] = $type;
            }
        }

        $result = [];
        foreach (self::$geo as $row) {
            if (is_null($row[self::$geoType . '_pagetitle']))
                continue;
            $return = [
                'id' => $row[self::$geoType . '_id'],
                'value' => $row[self::$geoType . '_pagetitle'],
            ];
            if (!empty($parentFields)) {
                $return['parent'] = $row[$parentFields[0] . '_id'];
                foreach ($parentFields as $parentField) {
                    $return[$parentField . '_id'] = $row[$parentField . '_id'];
                }
            }
            $result[$row[self::$geoType . '_id']] = $return;
        }

        self::$geo = null;
        self::$geoType = null;
        return $result;
    }
}
