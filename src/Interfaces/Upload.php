<?php

namespace Reich\Interfaces;

use Reich\Interfaces\Validator;

interface Upload
{
	/**
	 * Setter for async upload.
	 *
	 * @param bool | $flag
	 * @return $this
	 */
	public function async($flag = true): Upload;

	/**
	 * Sets the directory path where you 
	 * want to upload the files(if not specfied,
	 * files will be uploaded to the current directory).
	 *
	 * @param string | $path
	 * @return $this
	 */
	public function setDirectory($path): Upload;

	/**
	 * Create the directory if not exists.
	 * 
	 * @param bool|null  $create
	 * @return void
	 */
	public function create(?bool $create = null): void;

	/**
	 * Retrieve the validator.
	 * 
	 * @return \Reich\Interfaces\Validator
	 */
	public function validator(): Validator;

	/**
	 * Starts the upload process.
	 *
	 * @return void
	 */
	public function start();

	/**
	 * Checks if the upload was unsuccessful.
	 *
	 * @return bool
	 */
	public function unsuccessfulFilesHas(): bool;

	/**
	 * Checks if the upload was successful.
	 *
	 * @return bool
	 */
	public function successfulFilesHas(): bool;

	/**
	 * Retrieves the errors array 
	 * to give some feedback to the user.
	 *
	 * @return array
	 */
	public function errorFiles(): array;

	/**
	 * Retrieves the errors array 
	 * to give some feedback to the user.
	 *
	 * @return array
	 */
	public function successFiles(): array;

	/**
	 * Displays the errors 
	 * nicely formated with bootstraps.
	 *
	 * @return void
	 */
	public function displayErrors(): void;

	/**
	 * Displays the errors 
	 * nicely formated with bootstraps.
	 *
	 * @return void
	 */
	public function displaySuccess(): void;

	/**
	 * Checks if an upload 
	 * form has been submitted.
	 *
	 * @return bool
	 */
	public static function submitted(): bool;
}
