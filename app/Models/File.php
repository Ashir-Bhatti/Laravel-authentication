<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class File extends Model
{
    use HasFactory;
    use HasUuid, SoftDeletes;

    protected $guarded = ['id', 'uuid'];

    public function createImage($request, $type)
	{
		$ext = strtolower($request->extension());
		$name = explode('.', $request->getClientOriginalName())[0];
		$imageName = strtolower(Str::random(16)) . '.' . $request->extension();  
		$request->move(public_path('image'), $imageName);
		
		$file = File::create([
			'name' => $name,
			'path' => $imageName,
			'extension' => $ext,
			'type' => $type,
			'status' => 1
		]);
		return $file;
	}

    // public function removeImage($model, $old_dps)
	// {
	// 	if (count($old_dps) > 0) {
	// 		foreach ($old_dps as $old_dp) {
	// 			$model->files()->detach($old_dp->id);
	// 			delete_file($old_dp->id);
	// 		}
	// 	}
	// }

    //Relations
	public function organizations()
	{
		return $this->morphedByMany(Organization::class, 'fileable');
	}

	public function getUrlAttribute()
	{
		return url('/').'/image/'.$this->path;
	}
}
