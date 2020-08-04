<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class ImportContactCsvTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testImportLandingPage()
    {
        $response = $this->get('/contacts/import');
        $response->assertSee(__('import_contacts.import_csv'));
        $response->assertStatus(200);
    }

    public function testImportBehindLogin()
    {
        $csvFile = UploadedFile::fake()->create('document.csv', 30, 'text/csvs');
        $response = $this->json(
            'post', '/contacts/import/prepare', [
            'file' => $csvFile
            ]
        );
        $response->assertStatus(403);
    }

    public function testImportPrepare()
    {
        $csvFile = UploadedFile::fake()->create('document.csv', 30, 'text/csvs');
        $user = factory(User::class)->create();
        $response = $this->
            actingAs($user)
            ->json(
                'post', '/contacts/import/prepare',
                [
                'file' => $csvFile
                ]
            );
        $response->assertSee("headers");
    }

    public function additonalTests()
    {
        /**
         * Due to time constraits I was able to add them all,
         * but additonal tests would include posts to the contacts/import
         * verify the the contacts are inserted into the database
         * verify the the custom_attributes are imported into the database
         */
        $this->assertTrue(true);

    }

}
