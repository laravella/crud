<?php namespace Laravella\Crud;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CrudInstallCommand extends Command {

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
                $this->call('config:publish',array('package'=>'laravella/crud'));
                $this->call('asset:publish',array('package'=>'laravella/crud'));
                $this->call('migrate',array('--package'=>'laravella/crud'));
                
		$this->call('db:seed',array('--class'=>'DatabaseSeeder'));
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
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
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
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}