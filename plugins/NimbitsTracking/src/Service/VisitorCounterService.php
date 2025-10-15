<?php

namespace Nimbits\NimbitsTracking\Service;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Symfony\Component\HttpFoundation\Request;

class VisitorCounterService
{
    private SettingService $settings;
    private Connection $connection;

    public function __construct(
        SettingService $settingService,
        Connection     $connection
    )
    {
        $this->settings = $settingService;
        $this->connection = $connection;
    }

    public function countVisit(Request $request, ?string $partner)
    {
        if (!$this->settings->getSetting('enableVisitorCounter') ?? false) {
            return;
        }

        // Ignore blacklisted IPs
        if (in_array($request->getClientIp(), explode("\n", $this->settings->getSetting('visitorCounterIpBlacklist') ?? []))) {
            return;
        }

        // Ignore API and Admin Requests
        if (str_ends_with($request->getRequestUri(), '/admin') || str_contains($request->getRequestUri(), '/api/')) {
            return;
        }

        try {
            if (!empty($request->getClientIp())) {
                if ($this->settings->getSetting('overrideSession') ?? false) {
                    $statement = $this->connection->prepare(<<<SQL
INSERT INTO `nb_tracking_visitor_ips` (`day`, `ip`, `referrer`, `requests`) 
    VALUES (:date, :ip, :referrer, 1)
ON DUPLICATE KEY UPDATE 
    referrer = :referrer,
    requests = requests + 1
SQL
                    );
                } else {
                    $statement = $this->connection->prepare(<<<SQL
INSERT INTO `nb_tracking_visitor_ips` (`day`, `ip`, `referrer`, `requests`)
    VALUES (:date, :ip, :referrer, 1)
ON DUPLICATE KEY UPDATE
    requests = requests + 1
SQL
                    );
                }

                $statement->bindValue(':date', (new DateTime)->format('Y-m-d'));
                $statement->bindValue(':ip', inet_pton($request->getClientIp()));
                $statement->bindValue(':referrer', $partner ?? '');
                $statement->execute();
            }
        } catch (DBALException|DBALDriverException $e) {
            // TODO: Maybe add Logging?
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateVisitorCount()
    {
        $ips_table = $this->connection->createQueryBuilder()
            ->select('day', 'referrer', 'COUNT(ip) AS visitors', 'SUM(requests) AS requests')
            ->from('nb_tracking_visitor_ips')
            ->groupBy('day', 'referrer')
            ->execute()
            ->fetchAllAssociative();

        foreach ($ips_table as $row) {
            $stmnt = $this->connection->prepare(<<<SQL
INSERT INTO `nb_tracking_visitors` (`day`, `referrer`, `visitors`, `requests`) 
    VALUES (:date, :referrer, :visitors, :requests)
ON DUPLICATE KEY UPDATE 
    `visitors` = :visitors,
    `requests` = :requests
SQL
            );
            $stmnt->bindValue(':date', $row['day']);
            $stmnt->bindValue(':referrer', $row['referrer']);
            $stmnt->bindValue(':visitors', $row['visitors']);
            $stmnt->bindValue(':requests', $row['requests']);
            $stmnt->execute();
        }

        $this->connection->createQueryBuilder()
            ->delete('nb_tracking_visitor_ips')
            ->where('day < ?')
            ->setParameter(0, (new DateTime)->format('Y-m-d'))
            ->execute();
    }
}