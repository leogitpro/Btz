<?php
/**
 * DoctrineSqlLogger.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Service;


use Doctrine\DBAL\Logging\SQLLogger;


class DoctrineSqlLogger implements SQLLogger
{

    /**
     * @var AppLogger
     */
    private $logger;

    public function __construct(AppLogger $logger)
    {
        $this->logger = $logger;
    }

    public function startQuery($sql, array $params = null, array $types = null)
    {
        $content = 'ORM SQL Queries:' . PHP_EOL . $sql;
        if (!empty($params)) {
            foreach ($params as $k => $v) {
                if (!is_scalar($v)) {
                    $params[$k] = gettype($v);
                }
            }
            $content .= PHP_EOL . implode(', ', $params);
        }
        if (!empty($types)) {
            $content .= PHP_EOL . implode(', ', $types);
        }

        $this->logger->debug($content);
    }

    public function stopQuery()
    {
        // TODO: Implement stopQuery() method.
    }


}