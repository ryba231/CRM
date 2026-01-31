<?php

namespace App\Tests\Support;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class DatabaseWebTestCase extends WebTestCase
{
    protected function tearDown(): void
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $platform = $conn->getDatabasePlatform();

        $conn->executeStatement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($em->getMetadataFactory()->getAllMetadata() as $meta) {
            $conn->executeStatement(
                $platform->getTruncateTableSQL($meta->getTableName(), true)
            );
        }

        $conn->executeStatement('SET FOREIGN_KEY_CHECKS=1');

        parent::tearDown();
    }
}