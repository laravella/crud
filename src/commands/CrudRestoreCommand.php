<?php namespace Laravella\Crud;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CrudRestoreCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'crud:restore';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Restore a previous version of the meta data.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->call('db:seed',array('--class'=>'CrudRestoreSeeder'));
		$this->info('CRUD restore complete.');
		$this->info('Restore complete.');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('list', InputArgument::OPTIONAL, 'List all available backups.'),
			array('restore', InputArgument::OPTIONAL, 'Restore a specific backup, or the latest one if no id is specified.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('id', 'i', InputOption::VALUE_OPTIONAL, 'The id of the backup to restore. Use list argument to list available ids.', null),
		);
	}

}