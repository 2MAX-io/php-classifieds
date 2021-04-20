<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Command;

use App\Helper\FilePath;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Webmozart\PathUtil\Path;

/**
 * @internal
 */
class DeleteExpiredListingFilesCommandTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;

    public function test(): void
    {
        static::createClient();
        $kernel = static::$kernel;
        $application = new Application($kernel);

        $command = $application->find('app:delete-expired-listing-files');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'days' => 1,
            '--dry-run' => '--dry-run',
            '--limit' => 1,
        ]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] done', $output);
    }

    public function testDryRun(): void
    {
        static::createClient();
        $this->clearDatabase();
        $kernel = static::$kernel;

        $fileToRemovePath = FilePath::getProjectDir().'/static/listing/1/user_1/listing_9999/test_file_to_remove.png';
        $fileToRemovePathRelative = Path::makeRelative($fileToRemovePath, FilePath::getPublicDir());
        if (\file_exists($fileToRemovePath)) {
            \unlink($fileToRemovePath);
        }
        if (!\file_exists(\dirname($fileToRemovePath))) {
            \mkdir(\dirname($fileToRemovePath), 0775, true);
        }
        \copy(FilePath::getProjectDir().'/static/system/1920x1080.png', $fileToRemovePath);
        $this->executeSql(/** @lang MySQL */ <<<EOT
INSERT INTO `listing` (`id`, `category_id`, `user_id`, `expiration_date`, `admin_activated`, `admin_rejected`, `admin_removed`, `user_deactivated`, `user_removed`, `featured`, `featured_until_date`, `featured_priority`, `order_by_date`, `first_created_date`, `last_edit_date`, `admin_last_activation_date`, `title`, `description`, `price`, `price_for`, `price_negotiable`, `phone`, `email`, `email_show`, `location`, `location_latitude`, `location_longitude`, `main_image`, `slug`, `search_text`, `custom_fields_inline_json`, `rejection_reason`) VALUES (9999,140,1,'2010-01-01 00:00:00',0,0,0,1,0,0,NULL,0,'2021-03-22 06:49:00','2021-03-22 06:49:00','2021-03-22 06:49:00',NULL,'Test listing title','Tenetur provident et ut officiis voluptates. Quibusdam nihil quaerat qui fugit exercitationem in ad. Explicabo non maxime temporibus necessitatibus distinctio rerum officiis. Culpa omnis asperiores quis voluptas omnis dolor in.',158654.1,NULL,0,'+48536363636','user@example.com',0,'Lake Charley',51.416804797758,-0.09613476434808,'static/system/1920x1080.png','test-listing-title','At sit aliquam reprehenderit Tenetur provident et ut officiis voluptates. Quibusdam nihil quaerat qui fugit exercitationem in ad. Explicabo non maxime temporibus necessitatibus distinctio rerum officiis. Culpa omnis asperiores quis voluptas omnis dolor in.','[{\\"name\\":\\"Material\\",\\"value\\":\\"Tinware\\",\\"type\\":\\"SELECT_SINGLE\\",\\"unit\\":null}]',NULL);
INSERT INTO `listing_file` (`id`, `listing_id`, `path`, `filename`, `user_original_filename`, `mime_type`, `size_bytes`, `file_hash`, `image_width`, `image_height`, `user_removed`, `file_deleted`, `upload_date`, `sort`) VALUES (9999,9999,'{$fileToRemovePathRelative}','76a50887d8f1c2e9301755428990ad81479ee21c25b43215cf524541e0503269','test_file_to_remove.png','image/png',363636,'test_file_to_remove.png',1920,1080,0,0,'2021-03-22 06:49:00',2);
EOT);

        self::assertFileExists($fileToRemovePath);
        $application = new Application($kernel);
        $command = $application->find('app:delete-expired-listing-files');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'days' => 1,
            '--dry-run' => '--dry-run',
        ]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] done', $output);
        self::assertFileExists($fileToRemovePath);
        if (\file_exists($fileToRemovePath)) {
            \unlink($fileToRemovePath);
        }
    }

    public function testRemove(): void
    {
        static::createClient();
        $this->clearDatabase();
        $kernel = static::$kernel;

        $fileToRemovePath = FilePath::getProjectDir().'/static/listing/1/user_1/listing_9999/test_file_to_remove.png';
        $fileToRemovePathRelative = Path::makeRelative($fileToRemovePath, FilePath::getPublicDir());
        if (\file_exists($fileToRemovePath)) {
            \unlink($fileToRemovePath);
        }
        if (!\file_exists(\dirname($fileToRemovePath))) {
            \mkdir(\dirname($fileToRemovePath), 0775, true);
        }
        \copy(FilePath::getProjectDir().'/static/system/1920x1080.png', $fileToRemovePath);
        $this->executeSql(/** @lang MySQL */ <<<EOT
INSERT INTO `listing` (`id`, `category_id`, `user_id`, `expiration_date`, `admin_activated`, `admin_rejected`, `admin_removed`, `user_deactivated`, `user_removed`, `featured`, `featured_until_date`, `featured_priority`, `order_by_date`, `first_created_date`, `last_edit_date`, `admin_last_activation_date`, `title`, `description`, `price`, `price_for`, `price_negotiable`, `phone`, `email`, `email_show`, `location`, `location_latitude`, `location_longitude`, `main_image`, `slug`, `search_text`, `custom_fields_inline_json`, `rejection_reason`) VALUES (9999,140,1,'2010-01-01 00:00:00',0,0,0,1,0,0,NULL,0,'2021-03-22 06:49:00','2021-03-22 06:49:00','2021-03-22 06:49:00',NULL,'Test listing title','Tenetur provident et ut officiis voluptates. Quibusdam nihil quaerat qui fugit exercitationem in ad. Explicabo non maxime temporibus necessitatibus distinctio rerum officiis. Culpa omnis asperiores quis voluptas omnis dolor in.',158654.1,NULL,0,'+48536363636','user@example.com',0,'Lake Charley',51.416804797758,-0.09613476434808,'static/system/1920x1080.png','test-listing-title','At sit aliquam reprehenderit Tenetur provident et ut officiis voluptates. Quibusdam nihil quaerat qui fugit exercitationem in ad. Explicabo non maxime temporibus necessitatibus distinctio rerum officiis. Culpa omnis asperiores quis voluptas omnis dolor in.','[{\\"name\\":\\"Material\\",\\"value\\":\\"Tinware\\",\\"type\\":\\"SELECT_SINGLE\\",\\"unit\\":null}]',NULL);
INSERT INTO `listing_file` (`id`, `listing_id`, `path`, `filename`, `user_original_filename`, `mime_type`, `size_bytes`, `file_hash`, `image_width`, `image_height`, `user_removed`, `file_deleted`, `upload_date`, `sort`) VALUES (9999,9999,'{$fileToRemovePathRelative}','76a50887d8f1c2e9301755428990ad81479ee21c25b43215cf524541e0503269','test_file_to_remove.png','image/png',363636,'test_file_to_remove.png',1920,1080,0,0,'2021-03-22 06:49:00',2);
EOT);

        self::assertFileExists($fileToRemovePath);
        $application = new Application($kernel);
        $command = $application->find('app:delete-expired-listing-files');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'days' => 1,
        ]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] done', $output);
        self::assertFileDoesNotExist($fileToRemovePath);
        if (\file_exists($fileToRemovePath)) {
            \unlink($fileToRemovePath);
        }
    }
}
