<?php

namespace Tests;

use Source\Upload;
use Tests\Helpers\FileGenerator;

class UploadClassTest extends \PHPUnit_Framework_TestCase
{
	public function setUp() 
	{
		$this->fileGenerator = new FileGenerator;
	}

	public function tearDown()
	{
		unset($this->fileGenerator);
		unset($_FILES);
	}

	/** @test */
	public function order_files_properly()
	{
		$_FILES = $this->fileGenerator->single('file');

		$upload = new Upload('file');

		$results = $upload->sortFiles($_FILES['file']);

		$this->assertCount(1, $results);
		$this->assertCount(10, $results[0]);
		$this->assertArrayHasKey('encryption', $results[0]);
		$this->assertArrayHasKey('success', $results[0]);
		$this->assertArrayHasKey('errorMessage', $results[0]);

		$_FILES = $this->fileGenerator->multiple('files');

		$upload = new Upload('files');

		$results = $upload->sortFiles($_FILES['files']);

		$this->assertCount(2, $results);
		$this->assertCount(10, $results[0]);
		$this->assertCount(10, $results[1]);
		$this->assertArrayHasKey('encryption', $results[0]);
		$this->assertArrayHasKey('success', $results[0]);
		$this->assertArrayHasKey('errorMessage', $results[0]);

	}

	/** @test */
	public function can_add_rules() 
	{

		$_FILES = $this->fileGenerator->single('file');

		$upload = new Upload('file');

		$upload->addRules([
		        'size' => 2000,
		        'extensions' => 'png|jpg|pdf'
		]);

		$extensions = $upload->getAllowedExtensions();
		$maxSize = $upload->getMaxUploadingSize();
		
		$this->assertEquals($extensions, ['png', 'jpg', 'pdf']);
		$this->assertEquals($maxSize, 2000);

		$upload->addRules([
		        'size' => 2500,
		        'extensions' => ['png', 'jpg', 'pdf']
		]);

		$extensions = $upload->getAllowedExtensions();
		$maxSize = $upload->getMaxUploadingSize();

		$this->assertEquals($extensions, ['png', 'jpg', 'pdf']);
		$this->assertEquals($maxSize, 2500);
	}

	/** 
	 * @test 
	 * @expectedException \Source\Exceptions\InvalidRuleException
	 */
	public function an_exception_is_throwen_when_a_rule_that_does_not_exist_is_applied()
	{
		$_FILES = $this->fileGenerator->single('file');

		$upload = new Upload('file');

		$upload->addRules([
		        'size' => 2000,
		        'extensions' => 'png|jpg|pdf',
		        'notexist' => 'somevalue',
		]);
	}

	/** 
	 * @test 
	 * @expectedException InvalidArgumentException
	 */
	public function when_invalid_file_is_passed_an_exception_is_throwen()
	{
		$_FILES = $this->fileGenerator->invalidFile('file');

		$upload = new Upload('file');
	}
}