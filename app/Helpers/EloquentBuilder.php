<?php

namespace App\Helpers;

class EloquentBuilder
{

    /**
     * @param $builder
     * @return string|null
     */
    public static function getFullQuery($builder): ?string
    {
        $addSlashes = str_replace('?', "'?'", $builder->toSql());
        return vsprintf(str_replace('?', '%s', $addSlashes), $builder->getBindings());
    }

    public static function getFullSql($query)
    {
        $sqlStr = $query->toSql();
        foreach ($query->getBindings() as $iter => $binding) {

            $type = gettype($binding);
            switch ($type) {
                case 'integer':
                case 'double':
                    $bindingStr = $binding;
                    break;
                case 'string':
                    $bindingStr = '\''.$binding.'\'';
                    break;
                case 'object':
                    $class = get_class($binding);
                    switch ($class) {
                        case 'DateTime':
                            $bindingStr = "'" . $binding->format('Y-m-d H:i:s') . "'";
                            break;
                        default:
                            throw new \Exception('Unexpected binding argument class (' . $class . ')');
                    }
                    break;
                default:
                    throw new \Exception('Unexpected binding argument type (' . $type . ')');
            }

            $currentPos = strpos($sqlStr, '?');
            if ($currentPos === false) {
                throw new \Exception('Cannot find binding location in Sql String for bundung parameter ' . $binding . ' (' . $iter . ')');
            }

            $sqlStr = substr($sqlStr, 0, $currentPos) . $bindingStr . substr($sqlStr, $currentPos + 1);
        }

        $search = ["select", "distinct", "from", "where", "and", "order by", "asc", "desc", "inner join", "join"];
        $replace = ["SELECT", "DISTINCT", "\n  FROM", "\n    WHERE", "\n    AND", "\n    ORDER BY", "ASC", "DESC", "\n  INNER JOIN", "\n  JOIN"];
        $sqlStr = str_replace($search, $replace, $sqlStr);

        return $sqlStr;
    }

}
