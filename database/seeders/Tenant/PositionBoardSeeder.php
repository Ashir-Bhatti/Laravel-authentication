<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\PositionBoard;
use Illuminate\Database\Seeder;

class PositionBoardSeeder extends Seeder
{
	/**
	* Run the database seeds.
	*
	* @return void
	*/
	public function run()
	{
		$positionBoards = array(
			[
				'title' => 'Campaign Volunteers'
			],[
				'title' => 'Campaign Donors'
			],[
				'title' => 'Grassroots Activists'
			],[
				'title' => 'Delegates'
			],[
				'title' => 'Electors'
			],[
				'title' => 'Others'
				]
			);
			
		foreach ($positionBoards as $positionBoard) {
			PositionBoard::create($positionBoard);
		}
	}
}
